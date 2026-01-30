<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class ChatAIController extends Controller
{
    public function chat(Request $request)
    {
        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json(['reply' => 'Lỗi: Chưa cấu hình API Key']);
        }

        //Dùng model gemini-2.5-flash 
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        try {
            /** @var Response $response */
            $response = Http::retry(3, 1000)->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        ['parts' => [['text' => "Bạn là trợ lý ảo Holomia VR. Hãy tư vấn ngắn gọn: " . $userMessage]]]
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $botReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'AI không phản hồi.';
                return response()->json(['reply' => $botReply]);
            }

            return response()->json(['reply' => 'Lỗi Google (' . $response->status() . '): ' . $response->body()]);

        } catch (\Exception $e) {
            return response()->json(['reply' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }
}