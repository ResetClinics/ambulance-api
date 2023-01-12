import teamIcon from '../../assets/images/menu/team.png'
import teamIconColor from '../../assets/images/menu/team_color.png'
import currentCallIcon from '../../assets/images/menu/currentCall.png'
import currentCallIconColor from '../../assets/images/menu/currentCall_color.png'
import callHistoryIcon from '../../assets/images/menu/callHistory.png'
import callHistoryIconColor from '../../assets/images/menu/callHistory_color.png'
import profileIcon from '../../assets/images/menu/profile.png'
import profileIconColor from '../../assets/images/menu/profile_color.png'

export const icons = {
  Бригада: {
    default: teamIcon,
    focused: teamIconColor,
  },
  'Текущий вызов': {
    default: currentCallIcon,
    focused: currentCallIconColor,
  },
  'История вызовов': {
    default: callHistoryIcon,
    focused: callHistoryIconColor,
  },
  Профиль: {
    default: profileIcon,
    focused: profileIconColor,
  },
  default: {
    default: teamIcon,
    focused: teamIconColor,
  }
}
