# MyAI

A small personal AI chat app for XAMPP and PHP 8.1. It has a configurable assistant profile, browser-local conversation history, Markdown export, and a session-based API key setting. The model connection uses an OpenAI Chat Completions-compatible endpoint.

## Run locally

1. Put this folder under `C:\xampp8.1\htdocs\MYAI`.
2. Start Apache from the XAMPP Control Panel.
3. Open [http://localhost/MYAI/](http://localhost/MYAI/) in your browser.
4. Choose **Personalize**, enter the model ID and API key for your model provider, then save.

The endpoint defaults to `https://api.openai.com/v1/chat/completions`. You can change it for another compatible provider. Enter the model ID required by that provider. PHP's `curl` extension must be enabled for model requests; XAMPP commonly includes it.

## Personalization and storage

- Assistant name, personal context, custom instructions, and chat history are stored in this browser's local storage.
- The API key is held in the PHP session and is not written to browser storage or an app config file. It expires when the PHP session expires; use **Forget key** to remove it sooner.
- Prompts and chat messages are sent to the model provider you configure. Avoid putting sensitive information in the personal context unless you are comfortable sending it to that provider.
- Export the active conversation as a Markdown file with the download button in the top bar.

This is a local starter app. If you expose it to a network, add authentication and HTTPS before sharing it.
