import React from "react";
import { Layout } from "../../shared";
import { Text } from "react-native";
import { Avatar } from 'react-native-paper';
import { COLORS, SIZES } from "../../../constants";

export const Profile = () => {
  return (
    <Layout>
      <Avatar.Image size={140} source={require('../../../assets/image.webp')} style={styles.root} />
      <Text style={styles.title}>Иванов Иван Иванович</Text>
      <Text style={styles.text}>Невролог-терапевт</Text>
    </Layout>
  );
}

const styles = {
  title: {
    textAlign: 'center',
    fontSize: SIZES.fs18,
    lineHeight: 16,
    letterSpacing: 0.4,
    color: COLORS.black,
    marginBottom: 16
  },
  text: {
    textAlign: 'center',
    fontSize: SIZES.fs16,
    lineHeight: 16,
    letterSpacing: 0.4,
    color: COLORS.black
  },
  root: {
    marginLeft: 'auto',
    marginRight: 'auto',
    marginBottom: 16
  }
}
