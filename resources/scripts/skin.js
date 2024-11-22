document.querySelectorAll( "textarea.cdx-text-area__textarea--is-autosize" ).forEach(
	function ( textarea ) {
		textarea.addEventListener(
			"input",
			function ( event ) {
				event.currentTarget.style.height = 'auto';
				event.currentTarget.style.height = textarea.scrollHeight + "px";
			}
		);
	}
);
