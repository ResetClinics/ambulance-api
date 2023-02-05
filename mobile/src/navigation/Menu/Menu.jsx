/* eslint-disable */
import {
  Image,
  StyleSheet,
  Text, TouchableOpacity, View,
} from 'react-native'
import React, {useState} from 'react'
import Ionicons from 'react-native-vector-icons/Ionicons'
import {
  createDrawerNavigator, DrawerContentScrollView, DrawerItemList, DrawerToggleButton
} from '@react-navigation/drawer'
import { COLORS, FONTS } from '../../../constants'
import {
  Calls, CurrentCall, Profile, Team, History
} from '../../screens'
import penImg from '../../../assets/images/menu/pen.png'
import todayImg from '../../../assets/images/menu/today.png'
import {CalendarWindow, Select} from "../../components";
import Modal from "react-native-modal";

const Drawer = createDrawerNavigator()

const CustomDrawerContent = (props) => (
  <View style={styles.root}>
    <DrawerContentScrollView {...props}>
      <TouchableOpacity
        style={styles.outUser}
        onPress={() => props.navigation.closeDrawer()}
        activeOpacity={1}
      >
        <Ionicons name="arrow-back" size={20} color={COLORS.black} />
        <Text style={[styles.text, styles.black]}>Иванов Иван</Text>
      </TouchableOpacity>
      <DrawerItemList {...props} />
      <TouchableOpacity
        style={styles.out}
        onPress={() => props.handleSignOut()}
        activeOpacity={1}
      >
        <Ionicons name="exit-outline" size={18} color={COLORS.gray} />
        <Text style={styles.text}>Выйти</Text>
      </TouchableOpacity>
    </DrawerContentScrollView>
    <Text style={styles.copyright}>© 2014-2023 Клиника Респект</Text>
  </View>
)

export const Menu = ({ handleSignOut }) => {
  const [isModalVisible, setModalVisible] = useState(false)
  const [state, setState] = useState(false);

  const toggleSelect = () => {
    setState(!state);
  };
  const toggleModal = () => {
    setModalVisible(!isModalVisible)
  }
  return (
    <View style={styles.root}>
      <Drawer.Navigator
        screenOptions={{
          drawerActiveBackgroundColor: COLORS.primary,
          drawerActiveTintColor: COLORS.white,
          drawerLabelStyle: {
            fontSize: 14,
            marginLeft: -20,
          },
          overlayColor: COLORS.overlay,
          drawerPosition: 'right',
          /* headerLeft: () => null, */
          headerRight: () => <DrawerToggleButton tintColor="#04607A" />,
        }}
        drawerContent={(props) => <CustomDrawerContent {...props} handleSignOut={handleSignOut} />}
      >
        <Drawer.Screen
          name="team"
          component={Team}
          options={{
            drawerIcon: ({ color }) => (
              <Ionicons name="person-outline" size={18} color={color} />
            ),
            title: 'Бригада',
            headerLeft: () => null,
          }}
        />
        <Drawer.Screen
          name="сurrentCall"
          component={CurrentCall}
          options={{
            drawerIcon: ({ color }) => (
              <Ionicons name="person-outline" size={18} color={color} />
            ),
            title: 'Текущий вызов',
            headerLeft: () => null,
          }}
        />
        <Drawer.Screen
          name="сalls"
          component={Calls}
          options={{
            drawerIcon: ({ color }) => (
              <Ionicons name="ios-time-outline" size={18} color={color} />
            ),
            title: 'Вызовы',
            headerLeft: () => null,
          }}
        />
        <Drawer.Screen
          name="history"
          component={History}
          options={{
            drawerIcon: ({ color }) => (
              <Ionicons name="ios-time-outline" size={18} color={color} />
            ),
            title: 'История',
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
        <Drawer.Screen
          name="profile"
          component={Profile}
          options={{
            drawerIcon: ({ color }) => (
              <Ionicons name="person-outline" size={18} color={color} />
            ),
            title: 'Профиль',
            headerLeft: () => null,
          }}
        />
      </Drawer.Navigator>
      {
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
      }
      {
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
      }
    </View>
  )
}

const styles = StyleSheet.create({
  root: {
    flex: 1,
  },
  out: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 18,
    paddingHorizontal: 12,
    marginLeft: 8,
    width: '94%',
    borderTopColor: COLORS.darkGrey,
    borderTopWidth: 1
  },
  outUser: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 12,
    marginLeft: 5,
    paddingVertical: 18,
  },
  copyright: {
    textAlign: 'center',
    marginBottom: 12,
    ...FONTS.span,
  },
  text: {
    ...FONTS.span,
    color: COLORS.gray,
    marginLeft: 12
  },
  black: {
    color: COLORS.black,
  },
  img: {
    width: 24,
    height: 24,
    marginLeft: 16
  },
  holder: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  modal: {
    width: '100%',
    marginHorizontal: 0,
    marginBottom: 0,
    maxHeight: '80%',
    marginTop: 'auto',
  },
})
