# FastStop Plugin

![FastStop](https://img.shields.io/badge/Plugin-FastStop-brightgreen) ![Version](https://img.shields.io/badge/Version-1.0.0-blue)

## Overview

The **FastStop** plugin is designed for PocketMine-MP servers to apply various effects and restrict player actions during server updates. This plugin ensures that players cannot perform certain actions and receive effects to inform them of the update status. The plugin is highly customizable through its configuration file.

## Features

- **Apply Custom Effects:** Customize the effects applied to players during server updates.
- **Restrict Player Actions:** Prevent players from moving, interacting with blocks, placing or breaking blocks, and taking or causing damage.
- **Configurable Messaging:** Choose from different message types (title, tip, or chat message) to inform players of the update status.
- **Message Cooldown:** Set a cooldown to prevent message spam.

## Installation

1. Download the plugin zip file.
2. Unzip the file and put it in the `plugins` directory of your PocketMine-MP server.
3. Restart the server to load the plugin.
4. 
## Commands 

1. /faststop on: Enables FastStop mode, applying effects to all players and restricting actions.
2. /faststop off: Disables FastStop mode, removing effects from all players and lifting restrictions.
3. /faststop add PlayerName: removing effects from the player and lifting restrictions for him/her.
4. /faststop remove PlayerName: applying effects to the player and restricting actions for him/her.

   ***You can use the /fs command as aliase***
   
## Permissions

    faststop.command: Allows usage of the /faststop command.
    
## Configuration

The plugin uses a `settings.yml` file for configuration. This file is automatically created with default settings if it does not exist. 

### Default `settings.yml` Content

```yaml
# Configuration for FastStop Plugin
# ------------------------------
# Welcome to the FastStop plugin configuration!
# For more information or to request help, please visit the GitHub repository:
# https://github.com/ItzSasanW/FastStop
#
# This configuration file allows you to customize the effects applied to players, the messages displayed,
# and the actions that are prohibited when the server is in update mode.

# Effects:
# - Define the effects that should be applied to players when the server is in update mode.
# - Each effect entry must specify the effect name, duration (in ticks), and amplifier (strength).
# - The effect name should match PocketMine-MP's predefined effect names (e.g., "BLINDNESS", "SLOWNESS").
# - Duration is specified in ticks (1 second = 20 ticks).
# - Amplifier specifies the strength of the effect (0 = level 1, 1 = level 2, etc.).

effects:
  - effect: "BLINDNESS"
    duration: 800   # Duration in ticks (1 second = 20 ticks)
    amplifier: 1    # Effect strength

# Message:
# - Customize the type and content of the message displayed to players when they are affected by the update mode.
# - The message type can be "title" (full-screen title), "tip" (popup message), or "message" (standard chat message).
message:
  type: "title"
  content: "Server is updating!"

# Prohibited Actions:
# - Specify the actions that players are not allowed to perform while the server is in update mode.
# - Valid actions include:
#   - **"move"** (movement) 🌟
#   - **"interact"** (interacting with blocks) 🛠️
#   - **"place_block"** (placing blocks) 🚧
#   - **"break_block"** (breaking blocks) 💥
#   - **"damage"** (taking damage) ⚠️
#   - **"damage_by_entity"** (damage caused by entities) 🏹
prohibited_actions:
  - "move"
  - "interact"
  - "place_block"
  - "break_block"
  - "damage"
  - "damage_by_entity"

# Message Cooldown:
# - Duration in seconds between sending messages to prevent spamming.
message_cooldown: 2

