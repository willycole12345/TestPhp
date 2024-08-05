<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Please Enter your New OTP') }}
    </div>
    <div class="mb-4 text-xl text-rose-800">
      <p id="old">{{base64_decode($otpcode)}}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('Otpconfirmation') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="OTP" :value="__('Email')" />
            <x-text-input id="OTP" class="block mt-1 w-full" type="" name="OTP" />
            {{-- <x-input-error :messages="" class="mt-2" /> --}}
        </div>
        <x-text-input id="email" class="block mt-1 w-full" type="" name="email"  value="{{base64_decode($email)}}"/>
        <x-text-input id="otp" class="block mt-1 w-full" type="" name="otp"  value="{{base64_decode($otpcode)}}"/>
        
        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3" id="Newotp">
                Request for now otp
            </x-primary-button>

            <x-primary-button class="ms-3">    
            {{ __('Confirm OTP') }}
            </x-primary-button>
            
        </div>
    </form>
</x-guest-layout>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>

var site_base_url = "http://" + window.location.hostname + "/accelerate";
function fetch_baseurl() {
    return site_base_url;
}
console.log(fetch_baseurl);

    $(document).on('click','#Newotp',function(e){
        e.preventDefault();
            alert('tes');
    });

    $(document).ready(function(e){
        updateOTP();
    });

    function Request_for_otp(email,otp){

        var url = fetch_baseurl() +'/createotp/'+email +'/'+otp ;
        $.ajax({
            type: "GET",
            url: url,
            dataType: "json",
            success: function(res) {
           console.log(res);
            }
        });
    }
   
    function updateOTP(){
        setTimeout(function(){
        $('#old').hide();
    },30000);

    }
   
</script>

