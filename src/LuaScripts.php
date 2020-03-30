<?php

namespace TaivasAPM;

class LuaScripts
{
    /**
     * Get the Lua script to lpop multiple items from a list.
     *
     * KEYS[1] - The name of the list
     * ARGV[1] - The amount of items to pop from the list
     *
     * @return string
     */
    public static function lpopMany()
    {
        return <<<'LUA'
local key = KEYS[1]
local n = tonumber(ARGV[1])

local rep = {}
local ele = 1

while n > 0 and ele do
  ele = redis.call('LPOP', key)
  n = n - 1
  if ele then
    rep[#rep+1] = ele
  end
end

return rep
LUA;
    }
}
