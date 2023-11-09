<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://unpkg.com/tailwindcss@^2.0/dist/tailwind.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Conversation</title>
</head>
<body class="flex flex-col items-center justify-center min-h-screen bg-gray-100">

    <form method="POST" action="{{ route('conversations.store') }}" class="max-w-md p-6 bg-white rounded-lg shadow-lg">
        @csrf
        <h3 class="text-xl mb-4">Who would you like to chat with?</h3>
        <input type="text" name="user2_email" placeholder="Enter email" class="w-full px-3 py-2 border rounded-lg mb-4" required>
        <button>+</button>
        <button type="submit" class="bg-green-100 text-green-500 py-2 px-4 rounded-lg hover:bg-green-200 font-bold">Create!</button>
    </form>
    

    @if(session('error'))
        <div class="text-red-500 mt-4">
            {{ session('error') }}
        </div>
    @endif
    
</body>
</html>