<div @wrapperAttributes([ "block-testmonials-item" ])>
	<div class="post-info">

		<div class="left-area">
			<a href="#"><img src="https://i.pravatar.cc/150?img={{$index}}" class="img-cm" alt="Profile Image"></a>

		</div>

		<div class="middle-area">
			<a class="name" href="#"><b>{{$acf->user_name}}</b></a>
		</div>
	</div>

	<p>{{$acf->message}}</p>
</div>
