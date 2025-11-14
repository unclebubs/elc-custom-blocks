import path from 'node:path'
import { getBlockDirectories, execCommand, blocksRoot } from './utils.js'

async function installAll () {
  console.log('🚀 Installing dependencies for all blocks...\n')

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

  for (const blockDir of blockDirs) {
    const blockPath = path.join(blocksRoot, blockDir)
    console.log(`📦 Installing dependencies for ${blockDir}...`)

    const result = execCommand('npm install', blockPath, { verbose: false })

    if (result.success) {
      console.log(`✅ ${blockDir} - dependencies installed`)
      successCount++
    } else {
      console.log(`❌ ${blockDir} - installation failed`)
      failureCount++
    }
    console.log()
  }

  console.log(`\n📊 Installation Summary:`)
  console.log(`✅ Successful: ${successCount}`)
  console.log(`❌ Failed: ${failureCount}`)

  if (failureCount > 0) {
    process.exit(1)
  }
}

// Run if called directly
if (import.meta.url === `file://${process.argv[1]}`) {
  installAll().catch(error => {
    console.error('Installation failed:', error)
    process.exit(1)
  })
}

export default installAll
