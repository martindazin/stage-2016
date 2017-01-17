<h1>IMPORT & GESTION DES ABONNES</h1>

<script type="text/javascript">
	// Refresh page comme un "F5"
	// location.reload(true); 

	function openTab(evt, action) {
	    // Declare all variables
	    var i, tabcontentSubs, tablinks;

	    // Get all elements with class="tabcontentSubs" and hide them
	    tabcontentSubs = document.getElementsByClassName("tabcontentSubs");
	    for (i = 0; i < tabcontentSubs.length; i++) {
	        tabcontentSubs[i].style.display = "none";
	    }

	    // Get all elements with class="tablinks" and remove the class "active"
	    tablinks = document.getElementsByClassName("tablinks");
	    for (i = 0; i < tabcontentSubs.length; i++) {
	        tablinks[i].className = tablinks[i].className.replace(" active", "");
	    }

	    // Show the current tab, and add an "active" class to the link that opened the tab
	    document.getElementById(action).style.display = "block";
	    evt.currentTarget.className += " active";
	}
</script>

<style type="text/css">
 	/* Style the list */
	ul.tab {
	    list-style-type: none;
	    margin: 0;
	    padding: 0;
	    overflow: hidden;
	    border: 1px solid #ccc;
	    background-color: #f1f1f1;
	}

	/* Float the list items side by side */
	ul.tab li {float: left;}

	/* Style the links inside the list items */
	ul.tab li a {
	    display: inline-block;
	    color: black;
	    text-align: center;
	    padding: 14px 16px;
	    text-decoration: none;
	    transition: 0.3s;
	    font-size: 14px;
	}

	/* Change background color of links on hover */
	ul.tab li a:hover {background-color: #ddd;}

	/* Create an active/current tablink class */
	ul.tab li a:focus, .active {background-color: #ccc;}

	/* Style the tab content */
	.tabcontentSubs {
	    display: none;
	    padding: 6px 12px;
	    border: 1px solid #ccc;
	    border-top: none;
	}

	.tabcontentSubs {
	    -webkit-animation: fadeEffect 1s;
	    animation: fadeEffect 1s; /* Fading effect takes 1 second */
	}

	@-webkit-keyframes fadeEffect {
	    from {opacity: 0;}
	    to {opacity: 1;}
	}

	@keyframes fadeEffect {
	    from {opacity: 0;}
	    to {opacity: 1;}
	}
</style>

<?php
	// Fonctions PHP //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

	// Requêtes SQL //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');

    // Statut des persones :
		// - B : Blacklistés
		// - C : Confirmés
		// - D : Désinscrit
		// - N : Non-Confirmés
		// - T : Testeurs
?>

<ul class="tab">
		<li><a href="#" class="tablinks" onclick="openTab(event, 'subs_add')">Ajout d'abonnés</a></li>
		<li><a href="#" class="tablinks" onclick="openTab(event, 'no_subs_add')">Ajout de blacklistés</a></li>
		<li><a href="#" class="tablinks" onclick="openTab(event, 'test_add')">Ajout d'un testeur</a></li>
	<?php
		if ($nombreDeConfirmes > 0) {
			echo '<li><a href="#" class="tablinks" onclick="openTab(event, \'status_change\')">Changement de statut</a></li>';# code...
		}
		
	?>
</ul>

<?php
	echo '<div id="subs_add" class="tabcontentSubs">';
		include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/subs/subs_add.php');
	echo '</div>';
	echo '<div id="no_subs_add" class="tabcontentSubs">';
	 	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/subs/no_subs_add.php');
	echo '</div>';
	echo '<div id="test_add" class="tabcontentSubs">';
	 	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/subs/test_add.php');
	echo '</div>';
	if ($nombreDeConfirmes > 0) {
	echo '<div id="status_change" class="tabcontentSubs">';
		 	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/subs/status_change.php');
		echo '</div>';
	}
?>