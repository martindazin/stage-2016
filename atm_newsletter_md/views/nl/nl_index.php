<h1>NEWSLETTERS</h1>

<script type="text/javascript">

	// Refresh page comme un "F5"
	// location.reload(true); 

	function openTab(evt, action) {
	    // Declare all variables
	    var i, tabContentNl, tablinks;

	    // Get all elements with class="tabContentNl" and hide them
	    tabContentNl = document.getElementsByClassName("tabContentNl");
	    for (i = 0; i < tabContentNl.length; i++) {
	        tabContentNl[i].style.display = "none";
	    }

	    // Get all elements with class="tablinks" and remove the class "active"
	    tablinks = document.getElementsByClassName("tablinks");
	    for (i = 0; i < tabContentNl.length; i++) {
	        tablinks[i].className = tablinks[i].className.replace(" active", "");
	    }

	    // Show the current tab, and add an "active" class to the link that opened the tab
	    document.getElementById(action).style.display = "block";
	    evt.currentTarget.className += " active";
	}


  	scriptData = <?php echo json_encode($scriptData); ?>;

    alert(scriptData.message);
    document.getElementById('btn').onclick = function () {
        if (200 == scriptData.resultCode) {
            alert('user name : ' + scriptData.user.name)
        } else {
            alert('invalid Query !')
        }
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
	.tabContentNl {
	    display: none;
	    padding: 6px 12px;
	    border: 1px solid #ccc;
	    border-top: none;
	}

	.tabContentNl {
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

	// Status des nl :
		// - B : Brouillon
		// - E : Envoyé
		// - S : Sans tracking
		// - V : Validée

	
	echo '<ul class="tab">';
		echo '<li><a href="#" class="tablinks" onclick="openTab(event, \'params\')">Configurer les paramètres</a></li>';
		// Quand l'option atm_nl_periodiciteNl est validée, alors on affiche le panneau de création de nl
		if (get_option('atm_nl_periodiciteNl')) {
			echo '<li><a href="#" class="tablinks" onclick="openTab(event, \'create\')">Créer une newsletter</a></li>';
			// S'il y a au moins une nl de créée alors on peut l'éditer
			if ($nombreDeNl > 0) {
				echo '<li><a href="#" class="tablinks" onclick="openTab(event, \'edit\')">Êdition des newsletters</a></li>';
			}
		}
	echo '</ul>';

	echo '<div id="params" class="tabContentNl">';
		include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/nl/nl_params.php');
	echo '</div>';

	if (get_option('atm_nl_periodiciteNl')) {
		echo '<div id="create" class="tabContentNl">';
  			include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/nl/nl_create.php');
		echo '</div>';
		if ($nombreDeNl > 0) {
			echo '<div id="edit" class="tabContentNl">';
			 	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/nl/nl_edit.php');
			echo '</div>';
		}
	}
?>