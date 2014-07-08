<div id="footer">
	<pre><? print_r(Session::all()); ?></pre>
	You can always resume your session within the next 7 days by using the link below:<br />
	<a href="<?=action('MainController@Resume', $userid);?>"><?=action('MainController@Resume', $userid);?></a>
</div>
