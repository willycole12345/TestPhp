<x-guest-layout>
    <div class="mb-4 text-xl text-rose-800">
        <div class="text-rose-800" id="error"></div>
      </div>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Please Enter your New OTP') }}
    </div>
    <div class="mb-4 text-xl text-rose-800">
      <p id="old">{{base64_decode($otpcode)}}</p>
      <p id="new"></p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form>
        @csrf
        <div>
            <x-input-label for="OTP" :value="__('OTP')" />
            <x-text-input id="OTP" class="block mt-1 w-full" type="text" name="OTP" :value="old('OTP')"/>
        </div>
        <div>
        <x-text-input id="email" class="block mt-1 w-full" type="hidden" name="email"  value="{{base64_decode($email)}}"/>
        <x-text-input id="otp" class="block mt-1 w-full" type="hidden" name="otp"  value="{{base64_decode($otpcode)}}"/>
        </div>
        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3" id="Newotp">
                Request for now otp
            </x-primary-button>

            <x-primary-button class="ms-3" id="Confirm">    
            Confirm OTP
            </x-primary-button>
            
        </div>
    </form>
</x-guest-layout>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>

var site_base_url = "http://" + window.location.hostname + "";
function fetch_baseurl() {
    return site_base_url;
}
console.log(fetch_baseurl);

    $(document).on('click','#Newotp',function(e){
        e.preventDefault();
           // alert('tes');
            var email = $("#email").val();
           var otp = $("#otp").val();
           console.log(email);
           console.log(otp);
         Request_for_otp(email,otp);
    });

    $(document).on('click','#Confirm',function(e){
        e.preventDefault();
           var email = $("#email").val();
           var otp = $("#OTP").val();
           console.log(email);
           console.log(otp);
           Request_for_otp___(email,otp);
    });
    $(document).ready(function(e){
        updateOTP();
    });

    function Request_for_otp(email,otp){

        var url = fetch_baseurl() +'/createN/'+email +'/'+otp ;
        $.ajax({
            type: "GET",
            url: url,
            dataType: "json",
            success: function(res) {
                if (res.status == 'success') {
                    updateOTP();
               
                $('#otp').attr('value', res.messege);
                 $('#new').text(res.messege);
                }else{

                }
            }
        });
    }
    function Request_for_otp___(email,otp){

        var url = fetch_baseurl() +'/Otpconfirmation' ;
        console.log(url);
       
        var records = {
            "email":email,
            "otp":otp,
            "_token": "{{ csrf_token() }}",
        }

        $.ajax({
            type: "POST",
            url: url,
            data: records,
            dataType: "json",
            success: function(res) {
             console.log(res);

             if (res.status == 'success') {
                window.location.href= res.route;
             }else{
                $('#error').text(res.message);
             }
            }
        });
}
   
function cancelotp(email,otp){

    var url = fetch_baseurl() +'/updateotp/'+email +'/'+otp ;
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
        var email = $("#email").val();
           var otp = $("#otp").val();
           console.log(email);
           console.log(otp);
        cancelotp(email,otp);
    },30000);

    }
   
</script>

