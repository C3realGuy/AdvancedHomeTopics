Advanced Home Topics
====================

A plugin for wedge giving you more control over the topics block you can add to you Homepage->Custom Content.
Features:
  - Multiple working `topics` blocks
  - Change title of each `topics` block
  - Limit include boards
  - Limit exclude boards
  - Modify steps in which n increases

How to install?
===============

Drop the `advanced-home-topics` folder which you can find in this repository in to your `<wedge_install>/plugins` folder and activate it over your Admin Control Panel.

How to configure?
=================

1. Go to `Admin->Configuration->General Options->Homepage`
2. Modify `Custom Contents`
3. Add something like `topics:1|Some Special Posts|3;4||false|1;2;3;4;5;6;7`
4. Format looks like this `topics:<num posts to show by default>|<custom name, empty for default>|<include these boards, empty for all, divide with ;>|<exclude these boards, empty for none, divide with ;>|<set to true if you want to hide Boards>|<steps in which we shall increase. By default 5;10;20;50;100. Divide with ;>`
