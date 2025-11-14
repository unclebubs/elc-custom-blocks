# ELC Custom Blocks Plugin

This project creates a complete WordPress plugin containing custom Gutenberg blocks for the ELC website. Each subfolder contains a separate WordPress block that is its own npm project, and they're all bundled together into a single, deployable WordPress plugin.

## Quick Start

1. **Install dependencies for all blocks:**
   ```bash
   npm run install-all
   ```

2. **Build all blocks for production:**
   ```bash
   npm run build:production
   ```

3. **Deploy plugin to WordPress:**
   ```bash
   npm run deploy
   ```

## Environment Configuration

Copy `.env.example` to `.env` and configure your deployment path:

```bash
cp .env.example .env
```

Edit `.env`:
```
DEPLOY_TO="/path/to/your/wp-content/plugins/elc-blocks"
```

## Available Scripts

- `npm run install-all` - Install dependencies for all blocks
- `npm run build` - Build all blocks in development mode
- `npm run build:production` - Build all blocks in production mode
- `npm run start` - Start development mode for all blocks (watch mode)
- `npm run deploy` - Build and deploy plugin to WordPress
- `npm run clean` - Clean all build directories and node_modules
- `npm run lint` - Run linting for all blocks
- `npm run test` - Run tests for all blocks

## Block Structure

Each block directory contains:
- `package.json` - Block-specific dependencies and scripts
- `src/` - Source files (JS, CSS, PHP)
- `build/` - Compiled files (generated)
- `block.json` - WordPress block configuration

## Development Workflow

### Working on a single block

Navigate to the specific block directory and use standard WordPress block development:

```bash
cd bootstrap-pagination
npm start
```

### Working on all blocks

From the root blocks directory:

```bash
npm start  # Starts all blocks in watch mode
```

### Deploying changes

```bash
npm run deploy  # Builds and deploys all blocks
```

## Available Blocks

- account-login
- bootstrap-pagination
- elc-papers-tab-navigation
- journal-club-header
- journal-club-listings
- journal-year-navigation
- latest-journal-club-content
- most-recent-podcast
- most-viewed-podcast
- podcast-category-cards
- podcast-category-info
- podcast-listing
- podcast-search-form
- podcast-year-navigation
- summit-register-interest-buttons

## WordPress Plugin Structure

This project compiles into a complete WordPress plugin with the following structure:

```
elc-blocks/                    # Plugin directory
├── elc-blocks.php            # Main plugin file
├── readme.txt                # WordPress plugin readme
├── account-login/            # Individual block directories
│   ├── build/               # Compiled block assets
│   ├── account-login.php    # Block PHP logic
│   └── readme.txt           # Block documentation
└── [other blocks...]
```

## Deployment

The deployment script creates a complete WordPress plugin by copying:
- `elc-blocks.php` - Main plugin file with block registration
- Individual block directories with built assets and PHP files
- Plugin documentation and readme files

Excluded from deployment:
- `node_modules` - Development dependencies
- `src` - Source files (only built files deployed)
- `tests` - Test files
- Development configuration files

After deployment, activate the plugin in WordPress admin to make all blocks available in the Gutenberg editor.

## Plugin Benefits

- **Theme Independence**: Blocks persist when switching themes
- **Easy Updates**: Plugin can be updated independently
- **WordPress Standards**: Follows WordPress plugin development best practices  
- **Centralized Management**: All custom blocks in one plugin
- **Version Control**: Plugin versioning and update management
