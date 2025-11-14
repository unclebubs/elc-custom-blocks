import fs from 'node:fs/promises'
import path from 'node:path'
import { getBlockDirectories, execCommand, blocksRoot } from './utils.js'

async function testAll () {
  console.log('🧪 Running tests for all blocks...\n')

  const blockDirs = await getBlockDirectories()

  if (blockDirs.length === 0) {
    console.log('No block directories found with package.json files.')
    return
  }

  let successCount = 0
  let failureCount = 0
  let skippedCount = 0
  const failedBlocks = []
  const skippedBlocks = []

  for (const blockDir of blockDirs) {
    const blockPath = path.join(blocksRoot, blockDir)
    console.log(`🧪 Testing ${blockDir}...`)

    // Check if test script exists in package.json
    try {
      const packageJsonPath = path.join(blockPath, 'package.json')
      const packageJson = JSON.parse(await fs.readFile(packageJsonPath, 'utf8'))

      if (!packageJson.scripts?.test) {
        console.log(`⚠️  ${blockDir} - no test script found, skipping`)
        skippedCount++
        skippedBlocks.push(blockDir)
        continue
      }
    } catch (error) {
      console.log(`❌ ${blockDir} - error reading package.json`)
      failureCount++
      failedBlocks.push(blockDir)
      continue
    }

    const result = execCommand('npm test', blockPath, { verbose: false })

    if (result.success) {
      console.log(`✅ ${blockDir} - tests passed`)
      successCount++
    } else {
      console.log(`❌ ${blockDir} - tests failed`)
      failureCount++
      failedBlocks.push(blockDir)
    }
  }

  console.log(`\n📊 Testing Summary:`)
  console.log(`✅ Passed: ${successCount}`)
  console.log(`❌ Failed: ${failureCount}`)
  console.log(`⚠️  Skipped: ${skippedCount}`)

  if (skippedBlocks.length > 0) {
    console.log(`\n⚠️  Skipped blocks (no test script):`)
    skippedBlocks.forEach(block => console.log(`  - ${block}`))
  }

  if (failedBlocks.length > 0) {
    console.log(`\n❌ Failed blocks:`)
    failedBlocks.forEach(block => console.log(`  - ${block}`))
    process.exit(1)
  }

  console.log('\n🎉 All available tests passed!')
}

// Run if called directly
if (import.meta.url === `file://${process.argv[1]}`) {
  testAll().catch(error => {
    console.error('Testing failed:', error)
    process.exit(1)
  })
}

export default testAll
