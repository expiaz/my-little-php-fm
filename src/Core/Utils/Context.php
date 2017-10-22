<?php

namespace App\Core\Utils;

class Context extends ParameterBag
{

    /**
     * @param $field
     * @param bool|null $override does the added variable can erase old one if exists
     */
    protected function extract($field, ?bool $override = false)
    {
        if(! $this->has($field)){
            return;
        }

        if($override){
            extract([$field => $this->get($field)]);
        } else {
            extract([$field => $this->get($field)], EXTR_SKIP);
        }

        // to not corrupt the current scope's context
        unset($field, $override);
    }

    /**
     * @param string $key
     * @param $value
     * @param bool|null $override does the added variable can erase old one if exists
     */
    protected function add(string $key, $value, ?bool $override = false)
    {
        $this->set($key, $value);
        $this->extract($key, $override);
    }


    /**
     * extract every field to the current symbol table
     * @param bool|null $override
     */
    protected function deploy(?bool $override = false)
    {
        foreach ($this->fields as $field){
            $this->extract($field, $override);
        }
    }

}