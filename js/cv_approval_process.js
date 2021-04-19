var getUrlParameter = function getUrlParameter(sParam) {
  var sPageURL = window.location.search.substring(1),
      sURLVariables = sPageURL.split('&'),
      sParameterName,
      i;

  for (i = 0; i < sURLVariables.length; i++) {
    sParameterName = sURLVariables[i].split('=');

    if (sParameterName[0] === sParam) {
      return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
    }
  }
  return false;
};


cj(document).ready(function(){
  cj(document).on("focusin","#Screening_criteria_by_RCT #Screening_criteria_by_RCT", function(){
    cj('#CIVICRM_QFID_1_2').css('float','left');
    cj('#CIVICRM_QFID_1_2').css('margin-right','5px');
    cj("label[for='CIVICRM_QFID_1_2']").css('float', 'left');
    cj("label[for='CIVICRM_QFID_1_2']").css('margin-right','5px');
    cj('#CIVICRM_QFID_0_4').css('float','left');
    cj('#CIVICRM_QFID_0_4').css('margin-right','5px');
    cj("label[for='CIVICRM_QFID_0_4']").css('float', 'left');
    cj("label[for='CIVICRM_QFID_0_4']").css('margin-right','5px');

    cj('#CIVICRM_QFID_1_6').css('float', 'left');
    cj('#CIVICRM_QFID_1_6').css('margin-right','5px');
    cj("label[for='CIVICRM_QFID_1_6']").css('float', 'left');
    cj("label[for='CIVICRM_QFID_1_6']").css('margin-right','5px');
    cj('#CIVICRM_QFID_0_8').css('float', 'left');
    cj('#CIVICRM_QFID_0_8').css('margin-right','5px');
    cj("label[for='CIVICRM_QFID_0_8']").css('float', 'left');
    cj("label[for='CIVICRM_QFID_0_8']").css('margin-right','5px');

    cj('#CIVICRM_QFID_1_10').css('float', 'left');
    cj('#CIVICRM_QFID_1_10').css('margin-right','5px');
    cj("label[for='CIVICRM_QFID_1_10']").css('float', 'left');
    cj("label[for='CIVICRM_QFID_1_10']").css('margin-right','5px');
    cj('#CIVICRM_QFID_0_12').css('float', 'left');
    cj('#CIVICRM_QFID_0_12').css('margin-right','5px');
    cj("label[for='CIVICRM_QFID_0_12']").css('float', 'left');
    cj("label[for='CIVICRM_QFID_0_12']").css('margin-right','5px');

    cj('[data-crm-custom="Screening_criteria_by_RCT:Start_approval_process"]').css('float', 'left');
    cj('[data-crm-custom="Screening_criteria_by_RCT:Start_approval_process"]').css('margin-right', '5px');
    cj('[data-crm-custom="Screening_criteria_by_RCT:Start_approval_process"]').css('display', 'inline');

    cj('#Screening_criteria_by_RCT td:first-child').each(function() {
      cj(this).css('width', '30%');
    });

    cj('#Screening_criteria_by_RCT td:last-child label').css('display', 'inline');
  });
});