<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://unpkg.com/tailwindcss@^2.0/dist/tailwind.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
</head>
    <h1 class="flex justify-center text-4xl font-bold pt-4">
    {{ $recipient->name }}
    </h1>

    <div class="flex flex-col justify-center items-center min-h-screen pb-40">
        <div class="max-w-xl w-full">
            <div class="h-96 overflow-y-auto border border-gray-200 rounded-lg mb-4 bg-gray-100 shadow-lg">
                <ul class="p-4">
                    @foreach ($messages as $message)
                    <li class="mb-2">
                        <div class="@if ($message->user_id === $userId) flex justify-end @else justify-start @endif">
                            <div class="p-3 rounded-lg
                                @if ($message->user_id === $userId) bg-blue-500 text-white self-end @else bg-green-500 text-white self-start @endif"
                                style="max-width: 50%;">
                                {{ $message->content }}
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>

            <form method="POST" action="{{ route('messages.store', ['conversation_id' => $conversation->id]) }}" class="flex items-center">
                @csrf
                <input type="text" name="content" placeholder="Type Message..." class="flex-grow p-3 border rounded-lg shadow-lg">
                <button type="submit" class="ml-2 p-3 bg-green-400 hover:bg-green-500 rounded-lg">Send</button>
            </form>
        </div>
            <a href="/conversations" class="mt-5 p-3 bg-red-300 hover:bg-red-500 rounded-lg">back to Conversations</a>
    </div>





</body>
</html>
