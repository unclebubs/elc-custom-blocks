import fs from 'node:fs/promises'
import path from 'node:path'
import dotenv from 'dotenv'

// Load environment variables
dotenv.config()

const deployTo = process.env.DEPLOY_TO
const testBlockName = 'bootstrap-pagination'
const sourcePath = path.resolve(testBlockName)
const destPath = path.join(deployTo, testBlockName)

console.log(`🧪 Testing deployment of ${testBlockName}`)
console.log(`📁 Source: ${sourcePath}`)
console.log(`📁 Destination: ${destPath}`)

async function testDeploy () {
  // Remove existing destination
  await fs.rm(destPath, { recursive: true, force: true })

  // Create destination directory
  await fs.mkdir(destPath, { recursive: true })

  const entries = await fs.readdir(sourcePath, { withFileTypes: true })

  console.log('\n📋 Files in source directory:')
  for (const entry of entries) {
    const shouldSkip = [
      'node_modules',
      'src',
      'tests',
      '.editorconfig',
      '.gitignore',
      'package.json',
      'package-lock.json'
    ].includes(entry.name)

    if (shouldSkip) {
      console.log(`  ⏭️  ${entry.name} (skipped)`)
    } else {
      console.log(`  ✅ ${entry.name} (will copy)`)
    }
  }
}

testDeploy().catch(console.error)
