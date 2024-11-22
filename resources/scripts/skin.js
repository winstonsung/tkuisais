window.addEventListener(
	"message",
	function(event) {
		if (event.origin == "https://sso.tku.edu.tw") {
			document.querySelector("#token").value = event.data.tokenid;
			document.querySelector('#sso-iframe').style.display = "none";
		}
	},
	false
);
