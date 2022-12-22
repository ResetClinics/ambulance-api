import Ionicons from "react-native-vector-icons/Ionicons";
import React from "react";
import { createBottomTabNavigator } from "@react-navigation/bottom-tabs";
import { Brigade } from "../Brigade";
import { CurrentCall } from "../CurrentCall";
import { COLORS } from "../../../constants";
import { Image } from "react-native";
import {Chat} from "../Chat";

const Tab = createBottomTabNavigator();

const img1 = '../../../assets/menu/img1.png'
const img1_1 = '../../../assets/menu/img1_1.png'
const img2 = '../../../assets/menu/img2.png'
const img2_2 = '../../../assets/menu/img2_2.png'
const img3 = '../../../assets/menu/img3.png'
const img3_3 = '../../../assets/menu/img3_3.png'
const img4 = '../../../assets/menu/img4.png'
const img4_4 = '../../../assets/menu/img4_4.png'
const img5 = '../../../assets/menu/img5.png'
const img5_5 = '../../../assets/menu/img5_5.png'
const img6 = '../../../assets/menu/img6.png'
const img6_6 = '../../../assets/menu/img6_6.png'

const menuItems = [
  {
    name: "Бригада",
    component: Brigade
  },
  {
    name: "Текущий вызов",
    component: CurrentCall
  },
  {
    name: "История вызовов",
    component: CurrentCall
  },
  {
    name: "Чат",
    component: Chat
  },
  {
    name: "Уведомления",
    component: CurrentCall
  },
  {
    name: "Профиль",
    component: CurrentCall
  }
]

export const Menu = () => {
  return (
    <Tab.Navigator
      screenOptions={({route}) => ({
        tabBarStyle: {paddingTop: 4, height: 60, paddingBottom: 10},
        tabBarIcon: ({focused, color, size}) => {
          let iconName;

          if (route.name === 'Бригада') {
            iconName = focused ? img1 : img1_1;
          } else if (route.name === 'Текущий вызов') {
            iconName = focused ? img2 : img2_2;
          } else if (route.name === 'История вызовов') {
            iconName = focused ? img3 : img3_3;
          } else if (route.name === 'Чат') {
            iconName = focused ? img4 : img4_4;
          } else if (route.name === 'Уведомления') {
            iconName = focused ? img5 : img5_5;
          } else if (route.name === 'Профиль') {
            iconName = focused ? img6 : img6_6;
          }

          // You can return any component that you like here!
          return <Image source={require(img5)} size={size} color={color} style={styles.img}/>
        },
        tabBarActiveTintColor: COLORS.primary,
        tabBarInactiveTintColor: COLORS.gray,
      })}
    >
      <Tab.Screen name="Бригада" component={Brigade} style={styles.tab}/>
      <Tab.Screen name="Текущий вызов" component={CurrentCall} style={styles.tab}/>
      <Tab.Screen name="История вызовов" component={CurrentCall} style={styles.tab}/>
      <Tab.Screen name="Чат" component={Chat} style={styles.tab}/>
      <Tab.Screen name="Уведомления" component={CurrentCall} style={styles.tab} options={{tabBarBadge: 3}}/>
      <Tab.Screen name="Профиль" component={CurrentCall} style={styles.tab}/>
    </Tab.Navigator>
  );
}

const styles = {
  tab: {
    display: 'inline-block',
    fontSize: 25,
    color: COLORS.gray,
  },
  img: {
    width: 24,
    height: 24,
  }
}

