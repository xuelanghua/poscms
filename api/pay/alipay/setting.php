<tr>
	<th width="200">启用手机接口：</th>
	<td><input name="data[<?php echo $dir;?>][wap]" type="checkbox" <?php if ($t['wap']) { ?>checked="checked" <?php } ?> value="1" /></td>
</tr>
<tr>
	<th>支付宝账号(Email)：</th>
	<td><input type="text" name="data[<?php echo $dir;?>][username]" class="input-text" size="30" value="<?php echo $t['username']?>" /></td>
</tr><tr>
	<th>合作者身份(parterID)：</th> 
	<td><input type="text" name="data[<?php echo $dir;?>][id]" class="input-text" size="30" value="<?php echo $t['id']?>" /></td>
</tr>
<tr>
	<th>交易安全校验码(key)：</th> 
	<td><input type="text" name="data[<?php echo $dir;?>][key]" size="40" class="input-text" value="<?php echo $t['key']?>" /></td>
</tr>