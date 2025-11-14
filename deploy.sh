#!/bin/bash

# Simple deployment script for blocks
BLOCKS_SOURCE="/Volumes/T7/macos/Users/craig/Local Sites/elc-new/dev/blocks"
BLOCKS_DEST="/Volumes/T7/macos/Users/craig/Local Sites/elc-new/app/public/wp-content/themes/elc-theme/blocks"

echo "🚀 Deploying blocks..."
echo "Source: $BLOCKS_SOURCE"
echo "Destination: $BLOCKS_DEST"

# Create destination if it doesn't exist
mkdir -p "$BLOCKS_DEST"

# Copy each block directory
for block_dir in "$BLOCKS_SOURCE"/*; do
    if [ -d "$block_dir" ] && [ -f "$block_dir/package.json" ]; then
        block_name=$(basename "$block_dir")
        
        # Skip non-block directories
        if [[ "$block_name" == "node_modules" || "$block_name" == "scripts" || "$block_name" == ".git" ]]; then
            continue
        fi
        
        echo "📦 Deploying $block_name..."
        
        # Remove existing destination
        rm -rf "$BLOCKS_DEST/$block_name"
        
        # Create destination directory
        mkdir -p "$BLOCKS_DEST/$block_name"
        
        # Copy files, excluding development files
        rsync -av --exclude='node_modules' --exclude='src' --exclude='tests' --exclude='.git*' --exclude='.env*' --exclude='*.log' "$block_dir/" "$BLOCKS_DEST/$block_name/"
        
        echo "✅ $block_name deployed"
    fi
done

echo "🎉 All blocks deployed successfully!"
