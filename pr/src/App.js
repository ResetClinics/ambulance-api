import React from 'react';
import { NavigationContainer, DrawerActions, getFocusedRouteNameFromRoute } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import { createDrawerNavigator } from '@react-navigation/drawer';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';

import AccountScreen from './screens/Account';
import AdminScreen from './screens/Admin';
import LandingScreen from './screens/Landing';
import HomeScreen from './screens/Home';
import SignInScreen from "./screens/SignIn";
import {Button} from "react-native";
import SignUpScreen from "./screens/SignUp";
import PasswordForgetScreen from "./screens/PasswordForget";
import PasswordChangeScreen from "./screens/PasswordChange";
import ProfileScreen from "./screens/Profile";

const RootStack = createStackNavigator();

const Drawer = createDrawerNavigator();


const Tab = createBottomTabNavigator();

const HomeTabs = () => {
    return (
        <Tab.Navigator>
            <Tab.Screen
                name="HomeScreen" component={HomeScreen}
            />
            <Tab.Screen name="Profile" component={ProfileScreen} />
        </Tab.Navigator>
    );
};

const HomeDrawer = () => {

    return (
        <Drawer.Navigator>
            <Drawer.Screen
                name="HomeTabs"
                component={HomeTabs}
                options={({ route }) => ({
                    headerTitle: getFocusedRouteNameFromRoute(route),
                })}
            />
            <Drawer.Screen name="Account" component={AccountScreen} />
            <Drawer.Screen
                name="Password Forget"
                component={PasswordForgetScreen}
            />
            <Drawer.Screen
                name="Password Change"
                component={PasswordChangeScreen}
            />
            <Drawer.Screen name="Admin" component={AdminScreen} />
        </Drawer.Navigator>
    );
};

const App = () => {
    const [isAuthenticated, setIsAuthenticated] = React.useState(false);

    const handleSignOut = () => {
        setIsAuthenticated(false);
    };

    const handleSignIn = () => {
        setIsAuthenticated(true);
    };

    return (
        <NavigationContainer>
            <RootStack.Navigator>
                {isAuthenticated ? (
                    <>
                        <RootStack.Screen
                            name="App"
                            component={HomeDrawer}
                            options={({ route,navigation }) => ({
                                headerTitle: getFocusedRouteNameFromRoute(route),
                                headerRight: () => (
                                    <Button onPress={handleSignOut} title="Sign Out" />
                                ),
                                headerLeft: () => (
                                    <Button
                                        onPress={() =>
                                            navigation.dispatch(DrawerActions.toggleDrawer())
                                        }
                                        title="Menu"
                                    />
                                ),
                            })}
                        />
                    </>
                ) : (
                    <>
                        <RootStack.Screen name="Sign In">
                            {(props) => (
                                <SignInScreen {...props} onSignIn={handleSignIn} />
                            )}
                        </RootStack.Screen>
                        <RootStack.Screen
                            name="Password Forget"
                            component={PasswordForgetScreen}
                        />
                    </>
                )}
            </RootStack.Navigator>
        </NavigationContainer>
    );
};

export default App