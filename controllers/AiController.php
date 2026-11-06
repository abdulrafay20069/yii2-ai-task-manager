<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\Task;

class AiController extends Controller
{
    public $enableCsrfValidation = false; // sirf demo ke liye, simple rakhne ko

    public function actionIndex()
    {
        return $this->render('index');
    }

    // Chat: browser se history aati hai, hum AI se jawab lete hain
    public function actionChat()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $history = json_decode(Yii::$app->request->post('history', '[]'), true) ?: [];

        $messages = array_merge(
            [['role' => 'system', 'content' => 'You are a helpful assistant. Be concise.']],
            array_slice($history, -10)   // sirf aakhri 10 messages
        );
        return $this->ask($messages);
    }

    // Report: database ke tasks AI ko bhej kar summary mangna
    public function actionReport()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $tasks = Task::find()->orderBy(['priority' => SORT_ASC])->limit(100)->asArray()->all();
        if (!$tasks) {
            return ['reply' => 'Abhi koi task nahi hai.'];
        }

        $lines = array_map(
            fn($t) => "- [{$t['status']}] {$t['title']} (priority {$t['priority']}, due " . ($t['due_date'] ?: 'n/a') . ')',
            $tasks
        );

        $prompt = "Here are my tasks:\n" . implode("\n", $lines) .
            "\n\nWrite a short status report: progress summary, risks, and top 3 next steps. Plain text.";

        return $this->ask([
            ['role' => 'system', 'content' => 'You are a concise project analyst.'],
            ['role' => 'user', 'content' => $prompt],
        ], 0.3);
    }

    // Groq API call (wohi cheez jo Java mein OkHttp se ki thi)
    private function ask(array $messages, float $temp = 0.7): array
    {
        $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . (Yii::$app->params['groqApiKey'] ?? ''),
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'model' => 'openai/gpt-oss-120b',
                'messages' => $messages,
                'temperature' => $temp,
            ]),
        ]);
        $raw  = curl_exec($ch);
        $err  = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false) {
            Yii::$app->response->statusCode = 502;
            return ['error' => $err];
        }
        $data = json_decode($raw, true);
        if ($code !== 200) {
            Yii::$app->response->statusCode = 502;
            return ['error' => $data['error']['message'] ?? "HTTP $code"];
        }
        return ['reply' => $data['choices'][0]['message']['content'] ?? ''];
    }
}