<!DOCTYPE HTML>
<html>
	<head>
		<title>Utter News</title>
		<link rel="stylesheet" href="../static/style.css">
		<script>
function getTime() {
	var now = new Date();
	var text = (60 - now.getMinutes() - 1) + "' " + (60 - now.getSeconds()) + "\"";
	document.getElementById("air").innerHTML = text;
}
setInterval(getTime, 100);
		</script>
	</head>
	<body>
		<h1>Utter news authenticated upload</h1>
		<h2>Next airtime: <em id="air">Loading...</em></h2>
		<h2>Currently airing:</h2>
		<h2><audio controls src="https://data.ozva.co.uk/broadcast.mp3"></audio></h2>
		<h3>Welcome to the Utter News upload page.</h3>
		<p>Here you will upload your recorded news to the playout system, automatically scheduling it.</p>
		<h4>Guide</h4>
		<ol>
			<li>Record your news voice over, make sure to do this somewhere as quiet as possible. This can even be done on your phone. Don't worry about leaving long pauses, the proccessing system will remove these automatically!</li>
			<li>Pick your intro jingle, outro jingle and bed.</li>
			<li>Upload the file from your device or recorder.</li>
			<li>Click submit and it's done!'</li>
			<li>Your news will playout at the next top-of-the-hour oppertunity*.</li>
		</ol>
		<p><em>*Note that between 22:00 and 02:00, this will be replaced by Spaceweather.</em></p>
		<h4>Supported file types</h4>
		<p>.wav, .mp3, .vorbis, .ogg, .aif</p>
		<p style="padding: 5px; color: green; border: 1.5px solid green;">
			Note from will:<br>
			Just waiting on some idents from Jeremy, will hopefully give some more variety in the idents that can be picked!<br>
			Also if anything goes wrong pls let me know asap!
		</p>
		<form action="./index.php" method="post" enctype="multipart/form-data">
			<fieldset>
				<legend>Front ident</legend>
				<input type="radio" name="frontId" id="frontId" value="weather_in" checked="checked"/>
				<label for="frontId">Weather front ident</label><br>
				<audio controls src="assets/weather_in.wav"></audio><br>
			</fieldset>

			<fieldset>
				<legend>Bed</legend>
				<input type="radio" name="bedId" id="bedId" value="energetic_bed" checked="checked"/>
				<label for="bedId">Energetic bed</label><br>
				<audio controls src="assets/energetic_bed.wav"></audio><br>
				<input type="radio" name="bedId" id="bedId" value="weather_bed" checked="checked"/>
				<label for="bedId">Weather bed</label><br>
				<audio controls src="assets/weather_bed.wav"></audio><br>
				<input type="radio" name="bedId" id="bedId" value="mystery_bed" checked="checked"/>
				<label for="bedId">Mystery bed</label><br>
				<audio controls src="assets/mystery_bed.wav"></audio><br>
				<input type="radio" name="bedId" id="bedId" value="generic_bed" checked="checked"/>
				<label for="bedId">Utter news generic</label><br>
				<audio controls src="assets/generic_bed.wav"></audio><br>
			</fieldset>

			<fieldset>
				<legend>Back ident</legend>
				<input type="radio" name="backId" id="backId" value="generic_out" checked="checked"/>
				<label for="backId">Utter news generic</label><br>
				<audio controls src="assets/generic_out.wav"></audio><br>
			</fieldset>
			<br><label for="file">Select a file (.wav)</label>
			<br><input type="file" id="file" name="file"/>
			<br><br><input type="submit" name="upload" value="upload"/>
			<br><br><output>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

if (!empty($_POST['upload'])) {
	echo "Uploading...<br>";

	$fileType = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
	$targetFile = "assets/upload." . $fileType;
	$uploadOk = true;

	# remove the . to prevent sandbox escape
	$frontId = str_replace(".", "", $_POST["frontId"]);
	$bedId = str_replace(".", "", $_POST["bedId"]);
	$backId = str_replace(".", "", $_POST["backId"]);

	if($fileType != "wav" && $fileType != "mp3" && $fileType != "ogg" && $fileType != "vorbis" && $fileType != "aif") {
		echo "<span class='utter'>Only .wav and .mp3 files are currently allowed.</span><br>";
		$uploadOk = false;
	}

	if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile) and $uploadOk) {
		echo "<span class='green'>File successfully uploaded!</span><br>Processing...<br>";

		# convert to wav if not already
		if ($fileType != "wav") {
			exec("sox assets/upload.$fileType assets/upload.wav");
		}

		$output=null;
		$retval=null;
		exec("./script.sh -i $frontId -b $bedId -o $backId", $output, $retval);
		$output = var_export($output, true);
		if ($retval == "0") {
			echo "<span class='green'>Finished processing successfully.</span><br><br>";
			echo "<audio controls src='assets/news.wav' />";
		} else {
			echo "<span class='utter'>Error! (code $retval)<br>$output</span>";
		}
	} else {
		echo "<span class='utter'>Sorry, there was an error uploading your file.</span>";
	}
}
?>
			</output>
		</form>
	</body>
</html>
