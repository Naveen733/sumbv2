<div>
Thank you for signin up with us at SUMB<br><br>
Here are you full details:<br>
Name: {{$mydata['fullname']}}<br>
Email Address: {{$mydata['email']}}<br>
Account Type: {{$mydata['accountype']}}<br>
Date: {{$mydata['updated_at']}}<br><br>
You will need to verify your email, please <a href='{{$mydata['URL']}}verification?email={{$mydata['email']}}&token={{$mydata['remember_token']}}'>click here</a><br><br>
If the above link does not work please copy and paste this link to your browser:
<a href="{{$mydata['URL']}}verification?email={{$mydata['email']}}&token={{$mydata['remember_token']}}">{{$mydata['URL']}}verification?email={{$mydata['email']}}&token={{$mydata['remember_token']}}</a>
</div>