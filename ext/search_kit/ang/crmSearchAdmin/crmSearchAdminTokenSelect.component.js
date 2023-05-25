(function(angular, $, _) {
  "use strict";

  angular.module('crmSearchAdmin').component('crmSearchAdminTokenSelect', {
    bindings: {
      model: '<',
      field: '@',
      suffix: '@'
    },
    require: {
      admin: '^crmSearchAdmin'
    },
    templateUrl: '~/crmSearchAdmin/crmSearchAdminTokenSelect.html',
    controller: function ($scope, $element, searchMeta) {
      var ts = $scope.ts = CRM.ts('org.civicrm.search_kit'),
        ctrl = this;

      this.$onInit = function() {
        // Because this widget is so small, some placeholder text is helpful once it's open
        $element.on('select2-open', function() {
          $('#select2-drop > .select2-search > input').attr('placeholder', ts('Insert Token'));
        });
      };

      this.insertToken = function(key) {
        //ctrl.model[ctrl.field] = (ctrl.model[ctrl.field] || '') + '[' + key + ']';
        //ctrl.model[ctrl.field].selectionStart
        // but this doesn't work because focus is off this field when clicking in the select
        ctrl.model[ctrl.field] = ctrl.model[ctrl.field].slice(0,ctrl.model[ctrl.field].selectionStart) + '[' + key + ']' + ctrl.model[ctrl.field].slice(ctrl.model[ctrl.field].selectionEnd);
      };

      this.getTokens = function() {
        var allFields = ctrl.admin.getAllFields(ctrl.suffix || '', ['Field', 'Custom', 'Extra', 'Pseudo']);
        return {
          results: ctrl.admin.getSelectFields().concat(allFields)
        };
      };

      this.tokenSelectSettings = {
        data: this.getTokens,
        // The crm-action-menu icon doesn't show without a placeholder
        placeholder: ' ',
        // Make this widget very compact
        width: '52px',
        containerCss: {minWidth: '52px'},
        // Make the dropdown wider than the widget
        dropdownCss: {width: '250px'}
      };

    }
  });

})(angular, CRM.$, CRM._);
