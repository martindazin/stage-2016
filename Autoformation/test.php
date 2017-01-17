<html>

	<head>
		<meta charset="utf-8">
		<script type="text/javascript" src="./js/jquery.js"></script>
		<script type="text/javascript" src="./js/jquery.validate.js"></script>
		<script type="text/javascript" src="./js/additional-methods.js"></script>
		<script type="text/javascript" src="./js/messages_fr.js"></script>
		<script type="text/javascript" src="./js/script.js"></script>
	</head>

	
	<body>

  	<form name="form" action="">
      <div>        
        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" />
      </div>
      <div>        
        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" id="prenom" />
      </div>
      <div>        
        <label for="telephone">Téléphone :</label>
        <input type="text" name="telephone" id="telephone" />
      </div>
      <div>
        <label for="telephone">Email :</label>
        <input type="text" name="mail" id="mail">
      </div>
      <div>
        <label for="telephone">Mot de passe :</label>
        <input type="password" name="mdp1" id="mdp1">
      </div>
      <div>
        <label for="telephone">Mot de passe (confirmation) :</label>
        <input type="password" name="mdp2" id="mdp2">
      </div>
      <div>
        <input type="submit" name="bouton" value="Valider">
      </div>
      
	 </form>
				
	</body>

  <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
	<script>

    $(document).ready(function(){
      $('input[type="submit"]').on('click', function(event){
        event.preventDefault();
        
        // Partie nom
        var nom = document.getElementById("nom").value;
        var boolNom = false;
        var phraseNom = "";
        if(nom != ""){
          var regexNomPrenom = /^[a-zA-ZàâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$/;
          var testNom = regexNomPrenom.test(nom);
          if (testNom){
            boolNom = true;
          } else {
            phraseNom = "Votre nom est incorrect !";
          }
        } else {
          phraseNom = "Le champs nom est vide !";
        }

        // Partie prénom
        var prenom = document.getElementById("prenom").value;
        var boolPrenom = false;
        var phrasePrenom = "";
        if(prenom != ""){
          var regexNomPrenom = /^[a-zA-ZàâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$/;
          var testPrenom = regexNomPrenom.test(prenom);
          if (testPrenom){
            boolPrenom = true;
          } else {
            phrasePrenom = "Votre prénom est incorrect !";
          }
        } else {
          phrasePrenom = "Le champs prénom est vide !";
        }

        // Partie téléphone
        var telephone = document.getElementById("telephone").value;
        var boolTelephone = false;
        var phraseTelephone = "";
        if(telephone != ""){
          var regexTelephone = /^0[0-9]{9}/;
          var testTelephone = regexTelephone.test(telephone);
          if (testTelephone){
            boolTelephone = true;
          } else {
            phraseTelephone = "Votre numéro de téléphone est incorrect !"; 
          }
        } else {
          phraseTelephone = "Le champs télephone est vide !";
        }

        //Partie email
        var email = document.getElementById("mail").value;
        var boolEmail = false;
        var phraseEmail = "";
        if(email != ""){
          var regexEmail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          var testEmail = regexEmail.test(email);
          if (testEmail){
            boolEmail = true;
          } else {
            phraseEmail = "Votre email est incorrect !";
          }
        } else {
          phraseEmail = "Le champs email est vide !";
        }


        // Partie mdp1
        var mdp1 = document.getElementById("mdp1").value;
        var boolMdp1 = false;
        var phraseMdp1 = "";
        if(mdp1 != ""){
          boolMdp1 = true;
        } else {
          phraseMdp1 = "Le champs mot de passe est vide !";
        }
        

        // Partie mdp2
        var mdp2 = document.getElementById("mdp2").value;
        var boolMdp2 = false;
        var phraseMdp2 = "";
        if(mdp2 != ""){
          boolMdp2 = true;
        } else {
          phraseMdp2 = "Le champs mot de passe est vide !";
        }

        // Confirmation des mdp
        var boolConfirmationMdp = false;
        var phraseConfirmationMdp = "";
        if(mdp1 == mdp2){
          boolConfirmationMdp = true;
        } else {
          phraseConfirmationMdp = "Les deux mots de passe saisis ne sont pas pareils !";
        }


        // Affichage des messages d'erreurs          
        if (boolNom == false || boolPrenom == false || boolTelephone == false || boolEmail == false || boolConfirmationMdp == false){
          alert(phraseNom + " " + phrasePrenom + " " + phraseTelephone + " " + phraseEmail + phraseConfirmationMdp);
        } else {
          alert("Le remplissage du formulaire est correct !");
        }

      });
    });
	
	</script>

</html>
