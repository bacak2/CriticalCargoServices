<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2010 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.4, 2010-08-26
 */


/**
 * PHPExcel_Writer_Excel2007_Rels
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007_Rels extends PHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Write relationships to XML format
	 *
	 * @param 	PHPExcel	$pPHPExcel
	 * @return 	string 		XML Output
	 * @throws 	Exception
	 */
	public function writeRelationships(PHPExcel $pPHPExcel = null)
	{
		// Create XML writer
		$objWriter = null;
		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		} else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}

		// XML header
		$objWriter->startDocument('1.0','UTF-8','yes');

		// Relationships
		$objWriter->startElement('Relationships');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

			// Relationship docProps/app.xml
			$this->_writeRelationship(
				$objWriter,
				3,
				'http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties',
				'docProps/app.xml'
			);

			// Relationship docProps/core.xml
			$this->_writeRelationship(
				$objWriter,
				2,
				'http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties',
				'docProps/core.xml'
			);

			// Relationship xl/workbook.xml
			$this->_writeRelationship(
				$objWriter,
				1,
				'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument',
				'xl/workbook.xml'
			);

		$objWriter->endElement();

		// Return
		return $objWriter->getData();
	}

	/**
	 * Write workbook relation