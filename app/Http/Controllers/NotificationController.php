<?php

namespace App\Http\Controllers;

use App\Events\MessageReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\ImplementsFactory;

class NotificationController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $body = $request->input('message.body');
        event(new MessageReceived($body));
    }
}
