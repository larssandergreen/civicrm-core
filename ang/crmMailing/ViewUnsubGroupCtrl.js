(function(angular, $, _) {

  angular.module('crmMailing').controller('ViewUnsubGroupCtrl', function ViewUnsubGroupCtrl($scope) {
    var gids = [];
    var baseGroupNames = [];

    function getBaseGroupNames(mailing) {
    gidsToGet = [];
      _.each(mailing.recipients.groups.base, function(id) {
        if (-1 == gids.indexOf(parseInt(id))) {
          gidsToGet.push(parseInt(id));
          gids.push(parseInt(id));
        }
      });
      if (!_.isEmpty(gidsToGet)) {
        CRM.api3('Group', 'get', {'id': {"IN": gidsToGet}}).then(function(result) {
          _.each(result.values, function(grp) {
            if (_.isEmpty(_.where(baseGroupNames, {id: parseInt(grp.id)}))) {
              baseGroupNames.push({id: parseInt(grp.id), title: grp.title, is_hidden: grp.is_hidden});
            }
          });
        });
      }
    }

    $scope.getBasesAsString = function(mailing) {
      var first = true;
      var names = '';
      getBaseGroupNames(mailing);
      _.each(mailing.recipients.groups.base, function(id) {
        var group = _.where(baseGroupNames, {id: parseInt(id)});
        if (group.length) {
          if (!first) {
            names = names + ', ';
          }
          names = names + group[0].title;
          first = false;
        }
      });
      return names;
    };
  });

})(angular, CRM.$, CRM._);

