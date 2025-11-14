import fs from 'node:fs/promises'
import path from 'node:path'
import { fileURLToPath } from 'node:url'
import { getBlockDirectories, blocksRoot } from './utils.js'
import dotenv from 'dotenv'

// Load environment variables
dotenv.config()

const deployTo = process.env.DEPLOY_TO

if (!deployTo) {
  console.error('❌ DEPLOY_TO environment variable is not set.')
  console.error(
    'Please set it in your .env file or as an environment variable.'
  )
  process.exit(1)
}

async function deployBlocks () {
  console.log('🚀 Deploying blocks to WordPress theme...\n')
  console.log(`📁 Deployment target: ${deployTo}\n`)

  const blockDirs = await getBlockDirectories()

  if (blockDirs.length === 0) {
    console.log('No block directories found with package.json files.')
    return
  }

  // Ensure deployment directory exists
  try {
    await fs.mkdir(deployTo, { recursive: true })
  } catch (error) {
    console.error(`❌ Failed to create deployment directory: ${error.message}`)
    process.exit(1)
  }

  let successCount = 0
  let failureCount = 0
  const failedBlocks = []

  for (const blockDir of blockDirs) {
    const sourcePath = path.join(blocksRoot, blockDir)
    const destPath = path.join(deployTo, blockDir)

    console.log(`📦 Deploying ${blockDir}...`)

    try {
      // Remove existing destination
      await fs.rm(destPath, { recursive: true, force: true })

      // Create destination directory
      await fs.mkdir(destPath, { recursive: true })

      // Copy entire block directory
      await copyDirectory(sourcePath, destPath)

      console.log(`✅ ${blockDir} - deployed successfully`)
      successCount++
    } catch (error) {
      console.error(`❌ ${blockDir} - deployment failed: ${error.message}`)
      failureCount++
      failedBlocks.push(blockDir)
    }
  }

  console.log(`\n📊 Deployment Summary:`)
  console.log(`✅ Successful: ${successCount}`)
  console.log(`❌ Failed: ${failureCount}`)
  console.log(`📁 Deployed to: ${deployTo}`)

  if (failedBlocks.length > 0) {
    console.log(`\n❌ Failed deployments:`)
    failedBlocks.forEach(block => console.log(`  - ${block}`))
    process.exit(1)
  }

  console.log('\n🎉 All blocks deployed successfully!')
}

/**
 * Copy only the essential WordPress block files
 */
async function copyDirectory (src, dest) {
  const includePatterns = [
    'build', // Built assets
    '*.php', // WordPress PHP files
    'readme.txt', // WordPress readme
    'block.json' // Block configuration (if in root)
  ]

  const excludePatterns = [
    'node_modules',
    '.git',
    '.DS_Store',
    '.env',
    '.env.local',
    '*.log',
    'src', // Source files not needed in production
    'tests',
    '.editorconfig',
    '.gitignore',
    'package.json',
    'package-lock.json',
    '.babelrc'
  ]

  const entries = await fs.readdir(src, { withFileTypes: true })

  for (const entry of entries) {
    const shouldExclude = excludePatterns.some(pattern => {
      if (pattern.includes('*')) {
        return entry.name.endsWith(pattern.replace('*', ''))
      }
      return entry.name === pattern
    })

    if (shouldExclude) {
      console.log(`  ⏭️  Skipping ${entry.name}`)
      continue
    }

    // For directories, only include 'build' and exclude others not in include patterns
    if (entry.isDirectory() && entry.name !== 'build') {
      console.log(`  ⏭️  Skipping directory ${entry.name}`)
      continue
    }

    // For files, check if they should be included
    if (entry.isFile()) {
      const isPhpFile = entry.name.endsWith('.php')
      const isReadmeFile = entry.name === 'readme.txt'
      const isBlockJson = entry.name === 'block.json'

      if (!isPhpFile && !isReadmeFile && !isBlockJson) {
        console.log(`  ⏭️  Skipping file ${entry.name}`)
        continue
      }
    }

    const srcPath = path.join(src, entry.name)
    const destPath = path.join(dest, entry.name)

    console.log(`  📋 Copying ${entry.name}`)

    if (entry.isDirectory()) {
      await fs.mkdir(destPath, { recursive: true })
      await copyDirectoryRecursive(srcPath, destPath)
    } else {
      await fs.copyFile(srcPath, destPath)
    }
  }
}

/**
 * Recursively copy directory contents (for build folder)
 */
async function copyDirectoryRecursive (src, dest) {
  const entries = await fs.readdir(src, { withFileTypes: true })

  for (const entry of entries) {
    const srcPath = path.join(src, entry.name)
    const destPath = path.join(dest, entry.name)

    if (entry.isDirectory()) {
      await fs.mkdir(destPath, { recursive: true })
      await copyDirectoryRecursive(srcPath, destPath)
    } else {
      await fs.copyFile(srcPath, destPath)
    }
  }
}

// Run if called directly
const __filename = fileURLToPath(import.meta.url)
const isMainModule = process.argv[1] === __filename

if (isMainModule) {
  deployBlocks().catch(error => {
    console.error('Deployment failed:', error)
    process.exit(1)
  })
}

export default deployBlocks
