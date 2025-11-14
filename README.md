# ELC WordPress Blocks

This is the main npm project for managing all WordPress custom blocks for the ELC theme. Each subfolder contains a separate WordPress block that is its own npm project.

## Quick Start

1. **Install dependencies for all blocks:**
   ```bash
   npm run install-all
   ```

2. **Build all blocks for production:**
   ```bash
   npm run build:production
   ```

3. **Deploy all blocks to WordPress theme:**
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
DEPLOY_TO="/path/to/your/wp-content/themes/your-theme/blocks"
```

## Available Scripts

- `npm run install-all` - Install dependencies for all blocks
- `npm run build` - Build all blocks in development mode
- `npm run build:production` - Build all blocks in production mode
- `npm run start` - Start development mode for all blocks (watch mode)
- `npm run deploy` - Build and deploy all blocks to WordPress theme
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

## Deployment

The deployment script copies all necessary files to the WordPress theme's blocks directory, excluding:
- `node_modules`
- `src` (only built files are deployed)
- `tests`
- Development configuration files

This ensures only production-ready files are deployed to the WordPress site.
# elc-custom-blocks
