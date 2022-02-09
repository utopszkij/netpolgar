<div>
<h2>Megrendelés kezelési üzenet</h2>
<p> </p>
<p>
	<code>
	{!! $msg !!}
	</code>
</p>
<p> </p>
<p>Küldte: {{ $sender->name }}  </p>
<p><a href="mailto:{{ $sender->email }}">email</a></p>
<p> </p>
</div>
