import path from 'node:path'
import { getBlockDirectories, execCommand, blocksRoot } from './utils.js'

async function lintAll () {
  console.log('🔍 Running linting for all blocks...\n')

  const blockDirs = await getBlockDirectories()

  if (blockDirs.length === 0) {
    console.log('No block directories found with package.json files.')
    return
  }

  let successCount = 0
  let failureCount = 0
  const failedBlocks = []

  for (const blockDir of blockDirs) {
    const blockPath = path.join(blocksRoot, blockDir)
    console.log(`🔍 Linting ${blockDir}...`)

    // Try JS linting first
    let jsResult = execCommand('npm run lint:js', blockPath, { verbose: false })
    let cssResult = { success: true }

    // Try CSS linting if available
    try {
      cssResult = execCommand('npm run lint:css', blockPath, { verbose: false })
    } catch {
      // CSS linting might not be available
    }

    if (jsResult.success && cssResult.success) {
      console.log(`✅ ${blockDir} - linting passed`)
      successCount++
    } else {
      console.log(`❌ ${blockDir} - linting failed`)
      failureCount++
      failedBlocks.push(blockDir)
    }
  }

  console.log(`\n📊 Linting Summary:`)
  console.log(`✅ Passed: ${successCount}`)
  console.log(`❌ Failed: ${failureCount}`)

  if (failedBlocks.length > 0) {
    console.log(`\n❌ Failed blocks:`)
    failedBlocks.forEach(block => console.log(`  - ${block}`))
    process.exit(1)
  }

  console.log('\n🎉 All blocks passed linting!')
}

// Run if called directly
if (import.meta.url === `file://${process.argv[1]}`) {
  lintAll().catch(error => {
    console.error('Linting failed:', error)
    process.exit(1)
  })
}

export default lintAll
