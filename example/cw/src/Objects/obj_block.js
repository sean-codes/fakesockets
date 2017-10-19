obj_block = new Object();

obj_block.width = 16;
obj_block.height = 16;

obj_block.step = function(x, y, id)
{
  draw_sprite(spr_block, 0, x, y);
}
