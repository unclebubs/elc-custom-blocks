import fs from 'node:fs/promises'
import path from 'node:path'
import { fileURLToPath } from 'node:url'
import { execSync } from 'node:child_process'

const __dirname = path.dirname(fileURLToPath(import.meta.url))
const blocksRoot = path.resolve(__dirname, '..')

/**
 * Get all block directories that have package.json files
 */
async function getBlockDirectories () {
  try {
    const entries = await fs.readdir(blocksRoot, { withFileTypes: true })
    const blockDirs = []

    for (const entry of entries) {
      if (
        entry.isDirectory() &&
        entry.name !== 'scripts' &&
        entry.name !== 'node_modules'
      ) {
        const blockPath = path.join(blocksRoot, entry.name)
        const packageJsonPath = path.join(blockPath, 'package.json')

        try {
          await fs.access(packageJsonPath)
          blockDirs.push(entry.name)
        } catch {
          // Skip directories without package.json
        }
      }
    }

    return blockDirs
  } catch (error) {
    console.error('Error reading blocks directory:', error)
    return []
  }
}

/**
 * Execute a command in a specific directory
 */
function execCommand (command, cwd, options = {}) {
  const { verbose = false } = options

  if (verbose) {
    console.log(`📁 ${cwd}`)
    console.log(`🔧 ${command}`)
  }

  try {
    const result = execSync(command, {
      cwd,
      encoding: 'utf8',
      stdio: verbose ? 'inherit' : 'pipe'
    })

    if (!verbose && result) {
      console.log(result.trim())
    }

    return { success: true, output: result }
  } catch (error) {
    console.error(`❌ Error in ${path.basename(cwd)}: ${error.message}`)
    if (error.stdout) console.log(error.stdout)
    if (error.stderr) console.error(error.stderr)
    return { success: false, error }
  }
}

export { getBlockDirectories, execCommand, blocksRoot }
