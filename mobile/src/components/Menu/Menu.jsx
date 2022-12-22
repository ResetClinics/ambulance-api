import Ionicons from "react-native-vector-icons/Ionicons";
import React from "react";
import { createBottomTabNavigator } from "@react-navigation/bottom-tabs";
import { Brigade } from "../Brigade";
import { CurrentCall } from "../CurrentCall";
import {COLORS} from "../../../constants";
import { Image } from "react-native";

const Tab = createBottomTabNavigator();

const img1 = '../../../assets/menu/img1.png'
const img3 = '../../../assets/menu/img5.png'

export const Menu = () => {
  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        tabBarIcon: ({ focused, color, size }) => {
          let iconName;

          if (route.name === 'Бригада') {
            iconName = focused ? img1 : img1;
          } else if (route.name === 'Текущий вызов') {
            iconName = focused ? img1 : img1;
          } else if (route.name === 'История вызовов') {
            iconName = focused ? img1 : img1;
          } else if (route.name === 'Чат') {
            iconName = focused ? img1 : img1;
          } else if (route.name === 'Уведомления') {
            iconName = focused ? img1 : img1;
          } else if (route.name === 'Профиль') {
            iconName = focused ? img1 : img1;
          }

          // You can return any component that you like here!
          return <Image source={require(img3)} size={size} color={color} style={styles.img}/>
        },
        tabBarActiveTintColor: COLORS.primary,
        tabBarInactiveTintColor: COLORS.gray,
      })}
    >
      <Tab.Screen name="Бригада" component={Brigade} style={styles.tab} />
      <Tab.Screen name="Текущий вызов" component={CurrentCall} style={styles.tab} />
      <Tab.Screen name="История вызовов" component={CurrentCall} style={styles.tab} />
      <Tab.Screen name="Чат" component={CurrentCall} style={styles.tab} />
      <Tab.Screen name="Уведомления" component={CurrentCall} style={styles.tab} options={{ tabBarBadge: 3 }}/>
      <Tab.Screen name="Профиль" component={CurrentCall} style={styles.tab} />
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
    height: 24
  }
}

