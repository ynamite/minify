<?php
	$func = rex_request('func', 'string');
	
	if ($func == '') {
		$list = rex_list::factory("SELECT `id`, `name`, `type`, CONCAT('REX_MINIFY[type=',`type`,' set=',`name`,']') as `snippet` FROM `".rex::getTablePrefix()."minify_sets` ORDER BY `name` ASC");
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage($this->i18n('sets_norowsmessage'));
		
		// icon column
		$thIcon = '<a href="'.$list->getUrl(['func' => 'add']).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);
		
		$list->setColumnLabel('name', $this->i18n('sets_column_name'));
		$list->setColumnLabel('type', $this->i18n('sets_column_type'));
		$list->setColumnLabel('snippet', $this->i18n('sets_column_snippet'));
		
		$list->setColumnParams('name', ['id' => '###id###', 'func' => 'edit']);
		
		$list->removeColumn('id');
		
		$content = $list->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('content', $content, false);
		$content = $fragment->parse('core/page/section.php');
		
		echo $content;
	} else if ($func == 'add' || $func == 'edit') {
		$id = rex_request('id', 'int');
		
		if ($func == 'edit') {
			$formLabel = $this->i18n('sets_formcaption_edit');
		} elseif ($func == 'add') {
			$formLabel = $this->i18n('sets_formcaption_add');
		}
		
		$form = rex_form::factory(rex::getTablePrefix().'minify_sets', '', 'id='.$id);
		
		//Start - add name-field
			$field = $form->addTextField('name');
			$field->setLabel($this->i18n('sets_label_name'));
		//End - add name-field
		
		//Start - add type-field
			$field = $form->addSelectField('type');
			$field->setLabel($this->i18n('sets_label_type'));
			
			$select = $field->getSelect();
			$select->setSize(1);
			$select->addOption('---', 0);
			$select->addOption('CSS', 'css');
			$select->addOption('JS', 'js');
		//End - add type-field
		
		//Start - add attributes-field
			$field = $form->addTextAreaField('attributes');
			$field->setLabel($this->i18n('sets_label_attributes'));
			$field = $form->addRawField('<dl class="rex-form-group form-group"><dt>&nbsp;</dt><dd><p class="help-block rex-note">'.$this->i18n('sets_label_attributes_note').'</p></dd></dl>');
		//End - add attributes-field
		
		//Start - add output-field
			$field = $form->addSelectField('output');
			$field->setLabel($this->i18n('sets_label_output'));
			
			$select = $field->getSelect();
			$select->setSize(1);
			$select->addOption('Datei', 'file');
			$select->addOption('Inline', 'inline');
		//End - add output-field
		
		//Start - add assets-field
			$field = $form->addTextAreaField('assets');
			$field->setLabel($this->i18n('sets_label_assets'));
			$field = $form->addRawField('<dl class="rex-form-group form-group"><dt>&nbsp;</dt><dd><p class="help-block rex-note">'.$this->i18n('sets_label_assets_note').'</p></dd></dl>');
		//End - add assets-field
		
		if ($func == 'edit') {
			$form->addParam('id', $id);
		}
		
		$content = $form->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content, false);
		$content = $fragment->parse('core/page/section.php');
		
		echo $content;
	}
?>