/* eslint-disable */
import {
  Image,
  StyleSheet,
  TouchableOpacity, View,
} from 'react-native'
import React, { useState } from 'react'
import {
  createDrawerNavigator, DrawerToggleButton
} from '@react-navigation/drawer'
import Modal from 'react-native-modal'
import { COLORS } from '../../../constants'
import {
  Calls, CurrentCall, Profile, Team, History, Chat
} from '../../screens'
import penImg from '../../../assets/images/menu/pen.png'
import todayImg from '../../../assets/images/menu/today.png'
import { CalendarWindow, CustomDrawerContent, Select } from '../../components'
import brigadeImg from '../../../assets/images/mobile/brigade.png'
import brigadeDarkImg from '../../../assets/images/mobile/brigade-dark.png'
import bagImg from '../../../assets/images/mobile/bag.png'
import bagDarkImg from '../../../assets/images/mobile/bag-dark.png'
import callsImg from '../../../assets/images/mobile/calls.png'
import callsDarkImg from '../../../assets/images/mobile/calls-dark.png'
import chatImg from '../../../assets/images/mobile/chat.png'
import chatDarkImg from '../../../assets/images/mobile/chat-dark.png'
import profileImg from '../../../assets/images/mobile/profile.png'
import profileDarkImg from '../../../assets/images/mobile/profile-dark.png'
import historyImg from '../../../assets/images/mobile/history.png'
import historyDarkImg from '../../../assets/images/mobile/history-dark.png'

const Drawer = createDrawerNavigator()
const navigatorOptions = () => (
  {
    drawerActiveBackgroundColor: COLORS.primary,
    drawerActiveTintColor: COLORS.white,
    drawerLabelStyle: {
      fontSize: 14,
      marginLeft: -20,
      padding: 3,
      margin: 0
    },
    overlayColor: COLORS.overlay,
    drawerPosition: 'right',
    headerRight: () => <DrawerToggleButton tintColor="#04607A" />,
  }
)

const screenOptions = (title, icon, iconColor, headerLeft = () => null) => (
  {
    drawerIcon: ({ focused }) => (
      <View>
        {
            focused ? (
              <Image
                resizeMode="contain"
                source={icon}
                style={styles.img}
              />
            ) : (
              <Image
                resizeMode="contain"
                source={iconColor}
                style={styles.img}
              />
            )
          }
      </View>
    ),
    title,
    headerLeft,
  }
)

export const Menu = ({ handleSignOut }) => {
  const [isModalVisible, setModalVisible] = useState(false)
  const [state, setState] = useState(false)

  const toggleSelect = () => {
    setState(!state)
  }
  const toggleModal = () => {
    setModalVisible(!isModalVisible)
  }
  return (
    <View style={styles.root}>
      <Drawer.Navigator
        screenOptions={navigatorOptions}
        drawerContent={(props) => <CustomDrawerContent {...props} handleSignOut={handleSignOut} />}
      >
        <Drawer.Screen
          name="team"
          component={Team}
          options={screenOptions('Бригада', brigadeImg, brigadeDarkImg)}
        />
        <Drawer.Screen
          name="сurrentCall"
          component={CurrentCall}
          options={screenOptions('Текущий вызов', bagImg, bagDarkImg)}
        />
        <Drawer.Screen
          name="сalls"
          component={Calls}
          options={screenOptions('Вызовы', callsImg, callsDarkImg)}
        />
        <Drawer.Screen
          name="chat"
          component={Chat}
          options={screenOptions('Чат', chatImg, chatDarkImg)}
        />
        <Drawer.Screen
          name="profile"
          component={Profile}
          options={screenOptions('Профиль', profileImg, profileDarkImg)}
        />
        <Drawer.Screen
          name="history"
          component={History}
          options={{
            drawerIcon: ({ focused }) => (
              <View>
                {
                  focused ? (
                    <Image
                      resizeMode="contain"
                      source={historyImg}
                      style={styles.img}
                    />
                  ) : (
                    <Image
                      resizeMode="contain"
                      source={historyDarkImg}
                      style={styles.img}
                    />
                  )
                }
              </View>
            ),
            title: 'История заказов',
            headerLeft: () => (
              <View style={styles.holder}>
                <TouchableOpacity
                  onPress={toggleSelect}
                  activeOpacity={0.7}
                >
                  <Image
                    resizeMode="contain"
                    source={penImg}
                    style={styles.img}
                  />
                </TouchableOpacity>
                <TouchableOpacity
                  onPress={toggleModal}
                  activeOpacity={0.7}
                >
                  <Image
                    resizeMode="contain"
                    source={todayImg}
                    style={styles.img}
                  />
                </TouchableOpacity>
              </View>
            )
          }}
        />
      </Drawer.Navigator>
      <Modal
        style={styles.modal}
        isVisible={isModalVisible}
        backdropColor={COLORS.primary}
        backdropOpacity={0.4}
        animationInTiming={500}
        animationOutTiming={500}
        backdropTransitionOutTiming={600}
        onBackdropPress={toggleModal}
        onSwipeComplete={toggleModal}
        propagateSwipe
      >
        <CalendarWindow toggleModal={toggleModal} />
      </Modal>
      <Modal
        isVisible={state}
        animationIn="fadeIn"
        animationOut="fadeOut"
        onBackdropPress={toggleSelect}
        onSwipeComplete={toggleSelect}
        backdropColor="transparent"
      >
        <Select />
      </Modal>
    </View>
  )
}

const styles = StyleSheet.create({
  root: {
    flex: 1
  },
  img: {
    width: 24,
    height: 24,
    marginLeft: 6
  },
  holder: {
    flexDirection: 'row',
    alignItems: 'center',
    marginLeft: 10
  },
  modal: {
    width: '100%',
    marginHorizontal: 0,
    marginBottom: 0,
    maxHeight: '80%',
    marginTop: 'auto',
  },
})
