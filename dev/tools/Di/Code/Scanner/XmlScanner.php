<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright  Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Tools\Di\Code\Scanner;

class XmlScanner extends FileScanner
{
    /**
     * Regular expression pattern
     *
     * @var string
     */
    protected $_pattern = '/[\n\'"<>]{1}([A-Z]{1}[a-zA-Z0-9]*_[A-Z]{1}[a-zA-Z0-9_]*(Proxy|Factory))[\n\'"<>]{1}/';

    /**
     * Prepare xml file content
     *
     * @param string $content
     * @return string
     */
    protected function _prepareContent($content)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($content);

        $xpath = new \DOMXPath($dom);
        /** @var $comment \DOMComment */
        foreach ($xpath->query('//comment()') as $comment) {
            $comment->parentNode->removeChild($comment);
        }
        $output = $dom->saveXML();

        return $output;
    }

    /**
     * Filter found entities if needed
     *
     * @param array $output
     * @return array
     */
    protected function _filterEntities(array $output)
    {
        $filteredEntities = array();
        foreach ($output as $className) {
            $entityName = rtrim(preg_replace('/(Proxy|Factory)$/', '', $className), '_');
            // Skip aliases that are declared in Di configuration
            if (class_exists($entityName)) {
                array_push($filteredEntities, $className);
            }
        }
        return $filteredEntities;
    }
}