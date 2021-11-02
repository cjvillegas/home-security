import Orders from './Orders'
import Employee from './Employee'
import Notification from './Notification'
import User from './User'
import Reports from './Reports'
import Exports from './Exports'
import Scanners from './Scanners'
import Processes from './Processes'
import ProcessCategory from './ProcessCategory'
import ProcessSequence from './ProcessSequence'
import ProcessSequenceLink from './ProcessSequenceLink'
import Machine from './Machine'
import Role from './Role'
import Team from './Team'
import Shift from './Shift'

// Employee endpoint collections
import EmployeeAuth from './Employee/Auth'
import EmployeeEmployee from './Employee/Employee'

// Stock Order Collections
import StockOrder from './StockOrder/StockOrder'
import StockLevel from './StockOrder/StockLevel'
import PurchaseOrder from './StockOrder/PurchaseOrder'

export default {
    Orders,
    Employee,
    Notification,
    User,
    Reports,
    Exports,
    Processes,
    ProcessCategory,
    ProcessSequence,
    ProcessSequenceLink,
    Machine,
    Scanners,
    Role,
    Team,
    Shift,

    // employee dedicated endpoints
    EmployeeAuth,
    EmployeeEmployee,

    // Stock Order dedicated endpoints
    StockOrder,
    StockLevel,
    PurchaseOrder
}
