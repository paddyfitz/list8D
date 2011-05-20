<?php
class List8D_Theme_Root_Helper_Layout extends Zend_View_Helper_Layout {

		/**
     * Get layout object
     *
     * @return Zend_Layout
     */
    public function getLayout()
    {
        if (null === $this->_layout) {
            require_once 'Zend/Layout.php';
            $this->_layout = Zend_Layout::getMvcInstance();
            if (null === $this->_layout) {
                // Implicitly creates layout object
                $this->_layout = new List8D_Layout();
            }
        }

        return $this->_layout;
    }
    
}