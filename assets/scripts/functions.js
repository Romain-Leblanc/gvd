global.boutonModifier = function boutonModifier(num) {
    // Si la valeur du bouton radio est un nombre entier, on affichage le bouton "modifier"
    if(Number.isInteger(num)) {
        let btn = document.getElementById("bouton-modifier");
        btn.href = btn.baseURI+"/modifier/"+num;
        btn.classList.remove('cacher');
        btn.classList.add('afficher');
    }
}

global.boutonsFacture = function boutonModifier(num) {
    // Si la valeur du bouton radio est un nombre entier, on affichage le bouton "modifier"
    if(Number.isInteger(num)) {
        let btnModifier = document.getElementById("bouton-modifier");
        let btnTelecharger = document.getElementById("bouton-telecharger");
        let btnEnvoyer = document.getElementById("bouton-envoyer");

        btnModifier.href = btnModifier.baseURI+"/modifier/"+num;
        btnModifier.classList.remove('cacher');
        btnModifier.classList.add('afficher');

        btnTelecharger.href = btnTelecharger.baseURI+"/telecharger/"+num;
        btnTelecharger.classList.remove('cacher');
        btnTelecharger.classList.add('afficher');

        btnEnvoyer.href = btnEnvoyer.baseURI+"/envoyer/"+num;
        btnEnvoyer.classList.remove('cacher');
        btnEnvoyer.classList.add('afficher');
    }
}

global.getModeleFromMarque = function getModeleFromMarque(value) {
    // Récupération du bouton "submit"
    let btnSubmit = $('#btn-submit');
    // Vérifie si le DOM actuel contient l'élement 'ajout'
    if($("#ajout_vehicule_fk_modele").length > 0) {
        let inputModele = $("#ajout_vehicule_fk_modele");
        // Désactive les élements le temps de la requête Ajax
        inputModele.prop("disabled", true);
        btnSubmit.prop("disabled", true);

        // Si la "marque" est vide
        if(value !== "") {
            // Converti et vérifie si c'est un entier
            value = parseInt(value);
            if (Number.isInteger(value)) {
                // Requête Ajax pour les modèles de voitures
                ajaxQueryModele(inputModele, btnSubmit, value);
            }
        }
    }
    // Sinon si le DOM actuel contient l'élement 'modification'
    else if($("#modification_vehicule_fk_modele").length > 0) {
        let inputModele = $("#modification_vehicule_fk_modele");
        // Désactive les élements le temps de la requête Ajax
        inputModele.prop("disabled", true);
        btnSubmit.prop("disabled", true);

        // Si la "marque" est vide
        if(value !== "") {
            // Converti et vérifie si c'est un entier
            value = parseInt(value);
            if (Number.isInteger(value)) {
                ajaxQueryModele(inputModele, btnSubmit, value);
            }
        }
    }
}

global.enableBtnSubmitOnModele = function enableBtnSubmitOnModele(value) {
    // Active ou désactive le bouton de validation du formulaire en fonction de la valeur
    // du modèle d'une marque sélectionné
    let btnSubmit = $('#btn-submit');
    if(value !== undefined && value !== "") {
        btnSubmit.prop("disabled", false);
    }
    else {
        btnSubmit.prop("disabled", true);
    }
}

function ajaxQueryModele(inputModele, btnSubmit, value) {
    // Requête Ajax pour les modèles de voitures
    $.ajax({
        url : '/vehicule/infos',
        type: 'POST',
        data : {"marqueID": value},
        success: function(html) {
            let liste = "<option value='' selected='selected'>-- Modèle --</option>";
            // Concaténation des "options" du select
            html.donnees.forEach(element => liste += "<option value="+element.id+">"+element.modele+"</option>");
            // Vide les options actuelles du select puis les remplace
            inputModele.empty().append(liste);
            // Réactive les élements
            inputModele.prop("disabled", false);
        }
    });
}

global.getInfosFromClientIntervention = function getInfosFromClientIntervention() {
    let inputClient = $('#ajout_intervention_fk_client');
    let inputVehicule = $('#ajout_intervention_fk_vehicule');
    let inputEtat = $('#ajout_intervention_fk_etat');
    let inputDetailIntervention = $('#ajout_intervention_detail_intervention');
    let inputDureeIntervention = $('#ajout_intervention_duree_intervention');
    let inputMontantHT = $('#ajout_intervention_montant_ht');
    let btnSubmit = $('#btn-submit');
    inputVehicule.prop("disabled", true);
    inputEtat.prop("disabled", true);
    inputDetailIntervention.prop("disabled", true);
    inputDureeIntervention.prop("disabled", true);
    inputMontantHT.prop("disabled", true);
    btnSubmit.prop("disabled", true);

    $.post('/intervention/infos', {"clientID": inputClient.val()})
        .done(function(data) {
            if(data.donnees !== "" && data.donnees !== undefined) {
                let listeVehicule = "";
                // Concaténation des "options" du select
                data.donnees.forEach(element => listeVehicule += "<option value=" + element.id + ">" + element.fkMarque.marque + " " + element.fkModele.modele + " (" + element.immatriculation + ")" + "</option>");
                // Vide les options actuelles du select puis les remplace
                inputVehicule.empty().append(listeVehicule);
                // Supprime l'attribut 'disabled' des input concernés
                inputVehicule.prop('disabled', false);
                inputEtat.prop('disabled', false);
                inputDetailIntervention.prop('disabled', false);
                inputDureeIntervention.prop('disabled', false);
                inputMontantHT.prop('disabled', false);
                btnSubmit.prop('disabled', false);
            }
            else {
                // Vide les options actuelles du select puis ajoute la valeur par défaut
                inputVehicule.empty().append("<option value='' selected='selected'>-- VEHICULE --</option>");

                // Désactive les élements si il n'y aucune réponse
                inputVehicule.prop('disabled', true);
                inputEtat.prop('disabled', true);
                inputDetailIntervention.prop('disabled', true);
                inputDureeIntervention.prop('disabled', true);
                inputMontantHT.prop('disabled', true);
            }
        })
    ;
}

global.changeTotalFromTaux = function changeTotalFromTaux() {
    // Récupère les élements du DOM utilisés pour les calculs
    let selectTVA = $('#ajout_facture_fk_taux')[0];
    let divMontantHT = $('#total-ht')[0];
    let divMontantTVA = $('#total-tva')[0];
    let divMontantTTC = $('#total-ttc')[0];
    let inputHiddenMontantHT = $('#ajout_facture_montant_ht');
    let inputHiddenMontantTVA = $('#ajout_facture_montant_tva');
    let inputHiddenMontantTTC = $('#ajout_facture_montant_ttc');
    // Définit les valeurs par défaut
    let totalHT;
    let tauxTVA;
    let totalTVA;
    let totalTTC;

    totalHT = parseFloat(divMontantHT.textContent.split(' €')[0]);
    tauxTVA = calculTauxTVA(parseFloat(selectTVA.options[selectTVA.selectedIndex].innerText.split(" %")[0]));

    // Récupère les totaux des montants de TVA et TTC
    totalTVA = calculMontantTVA(totalHT, tauxTVA);
    totalTTC = calculMontantTTC(totalHT, totalTVA);

    // Affiche ces montants au format euros aux paragraphe concernés
    divMontantHT.innerHTML = formatMontantEuros(totalHT);
    divMontantTVA.innerHTML = formatMontantEuros(totalTVA);
    divMontantTTC.innerHTML = formatMontantEuros(totalTTC);

    // Définit la valeur des inputs cachés avec les montants précédents
    inputHiddenMontantHT.val(totalHT);
    inputHiddenMontantTVA.val(totalTVA);
    inputHiddenMontantTTC.val(totalTTC);
};

global.getInfosFromClientFacture = function getInfosFromClientFacture() {
    // Récupère les élements du DOM utilisés pour les calculs
    let selectTVA = $('#ajout_facture_fk_taux');
    let inputClient = $('#ajout_facture_fk_client');
    let tbodyTab = $('#table-interventions > tbody')[0];
    let inputMoyenPaiement = $('#ajout_facture_fk_moyen_paiement');
    let inputDatePaiement = $('#ajout_facture_date_paiement');
    let divMontantHT = $('#total-ht')[0];
    let divMontantTVA = $('#total-tva')[0];
    let divMontantTTC = $('#total-ttc')[0];
    let inputHiddenMontantHT = $('#ajout_facture_montant_ht');
    let inputHiddenMontantTVA = $('#ajout_facture_montant_tva');
    let inputHiddenMontantTTC = $('#ajout_facture_montant_ttc');
    let btnSubmit = $('#btn-submit');
    // Définit les valeurs par défaut
    let totalHT = 0;
    let totalTVA = 0;
    let totalTTC = 0;
    let tauxTVA = 0;
    let listeIntervention = "";

    $.post('/facture/infos', {"clientID": inputClient.val()})
        .done(function(data) {
             if(data.donnees !== "" && data.donnees !== undefined) {
                selectTVA.prop('disabled', false);
                // Récupère le taux de TVA au format décimal
                let tauxValue = selectTVA[0].options[selectTVA[0].selectedIndex].innerText.split(" %")[0];
                tauxTVA = calculTauxTVA(parseFloat(tauxValue));

                // Concaténation du détail des interventions dans le tableau et du total HT des interventions
                data.donnees.forEach(element => {
                    listeIntervention += '<tr class="tr-table-fact" id="tr-tab">' +
                        '<td width="15%" scope="row" class="align-middle" id="td-date-intervention">'+new Date(element.dateCreation).toLocaleDateString("fr")+'</td>' +
                        '<td width="20%" scope="row" class="align-middle" id="td-infos-vehicule">'+element.fkVehicule.fkMarque.marque+" - "+element.fkVehicule.fkModele.modele+'</td>' +
                        '<td width="35%" scope="row" class="align-middle" id="td-detail-intervention">'+element.detailIntervention+'</td>' +
                        '<td width="10%" scope="row" class="align-middle" id="td-duree-intervention">'+element.dureeIntervention+'h</td>' +
                        '<td width="20%" scope="row" class="align-middle" id="td-montant-ht">'+formatMontantEuros(element.montantHt)+'</td>' +
                        '</tr>';
                    totalHT += parseFloat(element.montantHt);
                })
                 // Remplace les valeurs du tableau avec les nouvelles interventions
                tbodyTab.innerHTML = listeIntervention;

                // Récupère les totaux des montants de TVA et TTC
                totalTVA = calculMontantTVA(totalHT, tauxTVA);
                totalTTC = calculMontantTTC(totalHT, totalTVA);

                // Affiche ces montants au format euros aux paragraphe concernés
                divMontantHT.innerHTML = formatMontantEuros(totalHT);
                divMontantTVA.innerHTML = formatMontantEuros(totalTVA);
                divMontantTTC.innerHTML = formatMontantEuros(totalTTC);

                // Définit la valeur des inputs cachés avec les montants précédents
                inputHiddenMontantHT.val(totalHT);
                inputHiddenMontantTVA.val(totalTVA);
                inputHiddenMontantTTC.val(totalTTC);

                // Réactive les éléments concernés
                inputMoyenPaiement.prop('disabled', false);
                inputDatePaiement.prop('disabled', false);
                btnSubmit.prop('disabled', false);
            }
            else {
                // Définit la valeur par défaut du tableau si la valeur récupérée est vide
                tbodyTab.innerHTML = '<td colspan="5" scope="row" class="align-middle">Veuillez sélectionner un client.</td>';

                // Réinitialise toutes les valeurs à 0
                divMontantHT.innerHTML = formatMontantEuros("0");
                divMontantTVA.innerHTML = formatMontantEuros("0");
                divMontantTTC.innerHTML = formatMontantEuros("0");

                inputHiddenMontantHT.val("0");
                inputHiddenMontantTVA.val("0");
                inputHiddenMontantTTC.val("0");

                // Désactive les élements
                selectTVA.prop('disabled', true);
                inputMoyenPaiement.prop('disabled', true);
                inputDatePaiement.prop('disabled', true);
                btnSubmit.prop('disabled', true);
            }
        });
}

// Retourne le montant en euros
function formatMontantEuros(montant){
    return parseFloat(montant).toFixed(2).replace('.', ',')+" €";
}

// Retourne le calcul du taux de TVA au format décimal
function calculTauxTVA(taux) {
    return parseFloat(parseFloat(taux)/100);
}

// Retourne le calcul du montant TVA
function calculMontantTVA(totalHT, tauxTVA) {
    return parseFloat(totalHT)*parseFloat(tauxTVA).toFixed(2);
}

// Retourne le calcul du montant TTC
function calculMontantTTC(totalHT, totalTVA) {
    return parseFloat(parseFloat(parseFloat(parseFloat(totalHT))+parseFloat(totalTVA)).toFixed(2));
}