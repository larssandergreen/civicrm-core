{*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
*}
    <div class="help">
    </div>
  <div class="crm-content-block crm-block">
    <table id="smart-group-profiler" class="display">
    <thead>
    <tr id="options" class="columnheader">
    <th id="sortable">{ts}Group name{/ts}</th>
    <th id="sortable">{ts}Duration (seconds){/ts}</th>
    </tr>
    </thead>
      {foreach from=$rows item=row}
        <tr id="smart-group-profile-{$row.id}" class="{cycle values="odd-row,even-row"}">
          <td data-field="title">{$row.title}</td>
          <td>{$row.duration|round:2}</td>
        </tr>
      {/foreach}
      </table>
  </div>
