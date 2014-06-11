<? include('inc/header.tpl.php') ?>

<h1><?= strtoupper($this->sH1) ?></h1>
<p><strong><?= $this->sDesc ?></strong></p>
<p>&nbsp;</p>

<? if(is_array($this->aBooks)): ?>

    <!-- A table of some books. -->
    <table>
        <tr>
            <th>Author</th>
            <th>Title</th>
        </tr>

        <? foreach($this->aBooks as $sKey => $sVal): ?>
            <tr>
                <td><?= $this->escape($sVal['author']) ?></td>
                <td><?= $this->escape($sVal['title']) ?></td>
            </tr>
        <? endforeach ?>

    </table>

    <? else: ?>

        <p>There are no books to display.</p>

    <? endif ?>

    <p>&nbsp;</p>
    <div id="html5" title="Validated for HTML5"></div>

<?php include('inc/footer.tpl.php') ?>
