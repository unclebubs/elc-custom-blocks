import path from 'node:path'
import { fileURLToPath } from 'node:url'
import { getBlockDirectories, execCommand, blocksRoot } from './utils.js'
import dotenv from 'dotenv'

// Load environment variables
dotenv.config()

const isProduction = process.argv.includes('--production')
const verbose = process.env.VERBOSE === 'true'

async function buildAll () {
  console.log(
    `🏗️  Building all blocks in ${
      isProduction ? 'PRODUCTION' : 'DEVELOPMENT'
    } mode...\n`
  )
  process.stdout.write('') // Force flush

  const blockDirs = await getBlockDirectories()

  if (blockDirs.length === 0) {
    console.log('No block directories found with package.json files.')
    return
  }

  console.log(`Found ${blockDirs.length} blocks:`)
  blockDirs.forEach(dir => console.log(`  - ${dir}`))
  console.log()

  let successCount = 0
  let failureCount = 0
  const failedBlocks = []

  for (const blockDir of blockDirs) {
    const blockPath = path.join(blocksRoot, blockDir)
    console.log(`🔨 Building ${blockDir}...`)

    const buildCommand = isProduction ? 'npm run build' : 'npm run build'
    const result = execCommand(buildCommand, blockPath, { verbose })

    if (result.success) {
      console.log(`✅ ${blockDir} - build completed`)
      successCount++
    } else {
      console.log(`❌ ${blockDir} - build failed`)
      failureCount++
      failedBlocks.push(blockDir)
    }
    console.log()
  }

  console.log(`\n📊 Build Summary:`)
  console.log(`✅ Successful: ${successCount}`)
  console.log(`❌ Failed: ${failureCount}`)

  if (failedBlocks.length > 0) {
    console.log(`\n❌ Failed blocks:`)
    failedBlocks.forEach(block => console.log(`  - ${block}`))
  }

  if (failureCount > 0) {
    process.exit(1)
  }

  console.log('\n🎉 All blocks built successfully!')
}

// Run if called directly
const __filename = fileURLToPath(import.meta.url)
const isMainModule = process.argv[1] === __filename

if (isMainModule) {
  buildAll().catch(error => {
    console.error('Build failed:', error)
    process.exit(1)
  })
}

export default buildAll
