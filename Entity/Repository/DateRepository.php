<?php
/**
 * Almanac.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://k62.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace RK\AlmanacModule\Entity\Repository;

use RK\AlmanacModule\Entity\Repository\Base\AbstractDateRepository;

/**
 * Repository class used to implement own convenience methods for performing certain DQL queries.
 *
 * This is the concrete repository class for date entities.
 */
class DateRepository extends AbstractDateRepository
{
    // feel free to add your own methods here, like for example reusable DQL queries
    protected $defaultSortingField = 'startDate';
}
