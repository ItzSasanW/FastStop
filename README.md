<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FastStop Plugin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        h1, h2, h3 {
            color: #1a73e8;
        }
        code {
            background-color: #f4f4f4;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 85%;
        }
        pre {
            background-color: #f4f4f4;
            padding: 10px;
            border-radius: 3px;
            overflow-x: auto;
        }
        .section {
            margin-bottom: 20px;
        }
        .highlight {
            background-color: #e8f0fe;
            border-left: 5px solid #1a73e8;
            padding: 10px;
            margin: 10px 0;
        }
        a {
            color: #1a73e8;
        }
        .footer {
            margin-top: 40px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>FastStop Plugin</h1>
    <p><strong>Version:</strong> 1.0.0</p>

    <div class="section">
        <h2>Overview</h2>
        <p>The FastStop Plugin is designed to manage player actions and apply effects during server updates. It provides functionality to enable or disable specific player actions and display customizable messages during the update process.</p>
    </div>

    <div class="section">
        <h2>Features</h2>
        <ul>
            <li>Enable or disable player actions during server updates.</li>
            <li>Apply various VanillaEffects to players when updates are in progress.</li>
            <li>Display customizable messages to players based on the selected message type.</li>
            <li>Prevent spamming by controlling the message cooldown period.</li>
        </ul>
    </div>

    <div class="section">
        <h2>Installation</h2>
        <p>1. Download the FastStop Plugin ZIP file from the repository.</p>
        <p>2. Extract the ZIP file into the <code>plugins</code> directory of your PocketMine server.</p>
        <p>3. Restart the server to load the plugin.</p>
    </div>

    <div class="section">
        <h2>Configuration</h2>
        <p>The plugin configuration file is located at <code>plugins/FastStop/settings.yml</code>. The default settings are as follows:</p>
        <pre><code>effects:
  - effect: "BLINDNESS"
    duration: 800   # Duration in ticks (1 second = 20 ticks)
    amplifier: 1    # Effect strength

message:
  type: "title"
  content: "Server is updating!"

prohibited_actions:
  - "move"
  - "interact"
  - "place_block"
  - "break_block"
  - "damage"
  - "damage_by_entity"

message_cooldown: 2</code></pre>
        <p>Adjust the settings according to your needs. For more details, refer to the inline comments in the <code>settings.yml</code> file.</p>
    </div>

    <div class="section">
        <h2>Commands</h2>
        <p>Use the following command to enable or disable FastStop mode:</p>
        <pre><code>/faststop &lt;enable|disable&gt;</code></pre>
        <ul>
            <li><code>/faststop enable</code> - Enables FastStop mode.</li>
            <li><code>/faststop disable</code> - Disables FastStop mode.</li>
        </ul>
        <p>Ensure you have the necessary permissions to execute this command.</p>
    </div>

    <div class="section">
        <h2>Support</h2>
        <p>For more information or to request help, please visit the GitHub repository:</p>
        <p><a href="https://github.com/ItzSasan/FastStop" target="_blank">FastStop GitHub Repository</a></p>
    </div>

    <div class="footer">
        <p>Thank you for using the FastStop Plugin! We hope it enhances your server management experience.</p>
    </div>
</body>
</html>
