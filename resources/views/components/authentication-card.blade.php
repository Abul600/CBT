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

            {{ $slot }} <!-- Password field is inside this -->
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
    
    /* Forgot Password Link Styling */
    a.forgot-password,
    a[href*="forgot-password"] {
        color: blue !important;
        font-weight: bold !important;
        transition: color 0.3s ease-in-out !important;
    }
    
    a.forgot-password:hover,
    a[href*="forgot-password"]:hover {
        color: red !important;
    }
</style>

<!-- JavaScript for Eye Icon -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let passwordField = document.querySelector("input[name='password']");
        if (passwordField) {
            let wrapper = document.createElement("div");
            wrapper.classList.add("relative");

            passwordField.parentNode.insertBefore(wrapper, passwordField);
            wrapper.appendChild(passwordField);

            let button = document.createElement("button");
            button.type = "button";
            button.classList.add("absolute", "inset-y-0", "right-2", "flex", "items-center", "text-gray-600", "hover:text-gray-900");
            button.innerHTML = '<i class="fas fa-eye"></i>';
            button.onclick = function () {
                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    button.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    passwordField.type = "password";
                    button.innerHTML = '<i class="fas fa-eye"></i>';
                }
            };

            wrapper.appendChild(button);
        }
    });
</script>

<!-- FontAwesome for Icons -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
