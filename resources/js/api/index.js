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

// Employee endpoint collections
import EmployeeAuth from './Employee/Auth'
import EmployeeEmployee from './Employee/Employee'

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

    // employee dedicated endpoints
    EmployeeAuth,
    EmployeeEmployee,
}
