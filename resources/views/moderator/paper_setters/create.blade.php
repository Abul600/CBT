<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 p-6 rounded-lg text-left shadow-xl">
            <h2 class="font-extrabold text-4xl text-white tracking-wide">✍️ Add Paper Setter</h2>
        </div>
    </x-slot>

    <div class="py-12 flex justify-center items-center min-h-screen bg-gradient-to-br from-blue-300 to-purple-400">
        <div class="relative w-full max-w-3xl bg-white rounded-3xl p-8 border border-gray-200 shadow-lg">
            <h3 class="text-3xl font-bold text-gray-900 text-center mb-6">🌟 Enter Your Details</h3>
            
            <form action="{{ route('moderator.paper_setters.store') }}" method="POST" class="grid grid-cols-2 gap-6">
                @csrf
                <div class="relative">
                    <input type="text" name="name" required class="fun-input peer" placeholder=" ">
                    <label class="fun-label">Full Name</label>
                </div>
                <div class="relative">
                    <input type="email" name="email" required class="fun-input peer" placeholder=" ">
                    <label class="fun-label">Email Address</label>
                </div>
                <div class="relative">
                    <input type="text" name="phone" required class="fun-input peer" placeholder=" ">
                    <label class="fun-label">Phone Number</label>
                </div>
                <div class="relative">
                    <input type="text" name="district" value="{{ auth()->user()->district }}" readonly class="fun-input bg-gray-100 cursor-not-allowed">
                    <label class="fun-label">District</label>
                </div>
                <div class="relative">
                    <input type="password" name="password" required class="fun-input peer" placeholder=" ">
                    <label class="fun-label">Password</label>
                </div>
                <div class="relative">
                    <input type="password" name="password_confirmation" required class="fun-input peer" placeholder=" ">
                    <label class="fun-label">Confirm Password</label>
                </div>
                <div class="col-span-2 mt-8 text-center">
                    <button type="submit" class="fun-button">Add Paper Setter</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .relative { position: relative; }
        .fun-label {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            font-size: 1rem;
            transition: all 0.3s;
            pointer-events: none;
            background: white;
            padding: 0 6px;
        }
        .peer:focus ~ .fun-label, .peer:not(:placeholder-shown) ~ .fun-label {
            top: 6px;
            left: 12px;
            font-size: 0.85rem;
            font-weight: bold;
            color: #e91e63;
        }
        .fun-input {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 2px solid #ddd;
            font-size: 1rem;
            color: #333;
            outline: none;
            transition: all 0.3s;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.15), -4px -4px 10px rgba(255, 255, 255, 0.8);
        }
        .fun-input:focus {
            border-color: rgb(228, 99, 142);
            box-shadow: 5px 5px 20px rgba(233, 30, 99, 0.3), -4px -4px 12px rgba(255, 255, 255, 0.9);
        }
        .fun-button {
            background: linear-gradient(135deg, #e91e63, #6a1b9a);
            padding: 14px 28px;
            font-size: 1.1rem;
            font-weight: bold;
            color: white;
            border-radius: 15px;
            box-shadow: 6px 6px 15px rgba(0, 0, 0, 0.2), -4px -4px 10px rgba(255, 255, 255, 0.3);
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }
        .fun-button:hover {
            background: linear-gradient(135deg, #6a1b9a, #e91e63);
            box-shadow: 8px 8px 20px rgba(0, 0, 0, 0.3), -4px -4px 12px rgba(255, 255, 255, 0.4);
            transform: translateY(-2px);
        }
        .fun-button:active {
            transform: translateY(2px);
            box-shadow: inset 3px 3px 6px rgba(0, 0, 0, 0.2), inset -3px -3px 6px rgba(255, 255, 255, 0.3);
        }
    </style>
</x-app-layout>
