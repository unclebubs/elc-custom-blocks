import path from 'node:path'
import { getBlockDirectories, execCommand, blocksRoot } from './utils.js'

async function startAll () {
  console.log('🚀 Starting development mode for all blocks...\n')

  const blockDirs = await getBlockDirectories()

  if (blockDirs.length === 0) {
    console.log('No block directories found with package.json files.')
    return
  }

  console.log(
    `Found ${blockDirs.length} blocks. Starting development servers...\n`
  )

  // We'll start them all concurrently using npm's concurrently package
  const commands = blockDirs.map(blockDir => {
    const blockPath = path.join(blocksRoot, blockDir)
    return `"cd ${blockPath} && npm start"`
  })

  const concurrentCommand = `npx concurrently ${commands.join(' ')}`

  console.log('🔧 Starting all blocks in watch mode...')
  console.log('Press Ctrl+C to stop all development servers.\n')

  execCommand(concurrentCommand, blocksRoot, { verbose: true })
}

// Run if called directly
if (import.meta.url === `file://${process.argv[1]}`) {
  startAll().catch(error => {
    console.error('Start failed:', error)
    process.exit(1)
  })
}

export default startAll
