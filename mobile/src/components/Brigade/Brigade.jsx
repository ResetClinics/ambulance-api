import {Image, Text} from "react-native";
import React from "react";
import { COLORS } from "../../../constants";
import {Btn, Layout} from "../../shared";

const img = '../../../assets/reload.png'

export const Brigade = () => {
  return (
    <Layout>
      <Text style={styles.text}>Бригада еще не сформирована</Text>
      <Image source={require(img)} style={styles.img}/>
      <Btn>Завершить смену</Btn>
    </Layout>
  );
}

const styles = {
  text: {
    fontSize: 30,
    color: COLORS.black,
  },
  img: {
    width: 48,
    height: 48,
    marginLeft: 'auto',
    marginRight: 'auto',
    marginTop: 24
  }
}
