# Yii2 AI Task Manager

A Yii2 + MySQL task manager with Groq AI integration.

## Features

- Task CRUD generated with Gii
- AI chat assistant
- AI-generated status report from database tasks
- Voice input via browser speech recognition API (Chrome/Edge)

## Setup

1. Install XAMPP and Composer, then start Apache and MySQL.
2. Clone this project into `C:\xampp\htdocs` and run `composer install`.
3. Create the MySQL database `yii2_ai_app` and import `database.sql`.
4. Create `config/params-local.php` with your `groqApiKey` value:

   ```php
   <?php
   return ['groqApiKey' => 'YOUR_GROQ_API_KEY'];
   ```

5. Run `php yii serve --port=8081` and open <http://localhost:8081>.

## Notes

- CSRF validation is disabled in `AiController` for demo simplicity; enable it in production.
- The model used via Groq is `openai/gpt-oss-120b`.
- Never commit the API key.