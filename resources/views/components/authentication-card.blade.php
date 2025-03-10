<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-cover bg-center relative"
     style="background-image: url('{{ asset('images/Abcd.jpeg') }}');">
    
    <!-- Dark Overlay for better readability -->
    <div class="absolute inset-0 bg-black bg-opacity-60"></div>

    <!-- Auth Box -->
    <div class="w-full sm:max-w-md mt-2 px-12 py-10 bg-cover bg-center shadow-2xl sm:rounded-2xl overflow-hidden relative text-white"
         style="background-image: url('{{ asset('images/123.webp') }}'); background-size: cover; background-position: center; border: 2px solid rgba(255, 255, 255, 0.4); box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.5);">
        
        <div class="p-10 bg-black bg-opacity-75 rounded-2xl shadow-2xl">
            <h2 class="text-2xl font-bold text-center mb-6 text-blue-300">Welcome</h2>
            <p class="text-sm text-gray-300 text-center mb-4">Login or Register to continue</p>

            {{ $slot }} <!-- This should already contain the Forgot Password link -->
        </div>
    </div>
</div>

<style>
    input {
        color: black !important;
        background-color: navy blue !important;
        border: 1px solid gray !important;
        padding: 10px;
        border-radius: 5px;
    }
    input::placeholder {
        color: gray !important;
    }
    label {
        color: yellow !important;
        font-weight: bold;
    }
    .text-gray-300, a.text-gray-300 {
        color: white !important;
    }
    a.text-blue-500 {
        color: blue !important;
        transition: color 0.3s ease-in-out;
    }
    a.text-blue-400:hover {
        color: lightgray !important;
    }
    
    /* Forgot Password Link Styling */
    a.forgot-password {
        color: red !important;
        font-weight: bold;
        transition: color 0.3s ease-in-out;
    }
    a.forgot-password:hover {
        color: darkred !important;
    }
</style>
