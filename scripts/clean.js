import fs from 'node:fs/promises'
import path from 'node:path'
import { getBlockDirectories, blocksRoot } from './utils.js'

async function clean () {
  console.log('🧹 Cleaning build directories and node_modules...\n')

  const blockDirs = await getBlockDirectories()

  if (blockDirs.length === 0) {
    console.log('No block directories found.')
    return
  }

  let cleanedCount = 0

  for (const blockDir of blockDirs) {
    const blockPath = path.join(blocksRoot, blockDir)
    console.log(`🗑️  Cleaning ${blockDir}...`)

    try {
      // Clean build directory
      const buildPath = path.join(blockPath, 'build')
      await fs.rm(buildPath, { recursive: true, force: true })

      // Clean node_modules
      const nodeModulesPath = path.join(blockPath, 'node_modules')
      await fs.rm(nodeModulesPath, { recursive: true, force: true })

      console.log(`✅ ${blockDir} - cleaned`)
      cleanedCount++
    } catch (error) {
      console.log(`⚠️  ${blockDir} - clean skipped (${error.message})`)
    }
  }

  // Also clean root node_modules
  try {
    const rootNodeModules = path.join(blocksRoot, 'node_modules')
    await fs.rm(rootNodeModules, { recursive: true, force: true })
    console.log(`✅ Root node_modules - cleaned`)
  } catch (error) {
    console.log(`⚠️  Root node_modules - clean skipped (${error.message})`)
  }

  console.log(`\n🎉 Cleaned ${cleanedCount} blocks`)
}

// Run if called directly
if (import.meta.url === `file://${process.argv[1]}`) {
  clean().catch(error => {
    console.error('Clean failed:', error)
    process.exit(1)
  })
}

export default clean
